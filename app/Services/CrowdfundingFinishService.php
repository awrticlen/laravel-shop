<?php

namespace App\Services;

use App\Jobs\RefundCrowdfundingOrderJob;
use App\Models\CrowdfundingProduct;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class CrowdfundingFinishService
{
    /**
     * 众筹到期结算。延迟任务传入 $expectedVersion，与库中 finish_schedule_version 不一致则直接返回（改期后旧任务作废）。
     * 定时命令传 null，不校验版本，仅依赖「已到期 + 仍为 funding」。
     *
     * @return bool 本次是否执行了结算逻辑；因已结束/未到期/版本不符而未执行时为 false
     */
    public function finish(CrowdfundingProduct $cf, ?int $expectedVersion = null): bool
    {
        $orderIds = [];

        $didFinish = DB::transaction(function () use ($cf, $expectedVersion, &$orderIds) {
            /** @var CrowdfundingProduct|null $row */
            $row = CrowdfundingProduct::query()->lockForUpdate()->find($cf->getKey());

            if (! $row || $row->status !== CrowdfundingProduct::STATUS_FUNDING) {
                return false;
            }

            if ($expectedVersion !== null && (int) $row->finish_schedule_version !== $expectedVersion) {
                return false;
            }

            if (now()->lt($row->end_at)) {
                return false;
            }

            if ((float) $row->total_amount >= (float) $row->target_amount) {
                $row->update(['status' => CrowdfundingProduct::STATUS_SUCCESS]);

                return true;
            }

            $row->update(['status' => CrowdfundingProduct::STATUS_FAIL]);

            $orderIds = Order::query()
                ->where('type', Order::TYPE_CROWDFUNDING)
                ->whereNotNull('paid_at')
                ->where('refund_status', Order::REFUND_STATUS_PENDING)
                ->whereHas('items', function ($query) use ($row) {
                    $query->where('product_id', $row->product_id);
                })
                ->pluck('id')
                ->all();

            return true;
        });

        if (! $didFinish || $orderIds === []) {
            return $didFinish;
        }

        foreach ($orderIds as $orderId) {
            RefundCrowdfundingOrderJob::dispatch($orderId);
        }

        return true;
    }
}
