<?php

namespace App\Console\Commands;

use App\Models\CrowdfundingProduct;
use App\Services\CrowdfundingFinishService;
use Illuminate\Console\Command;

class FinishDueCrowdfunding extends Command
{
    protected $signature = 'crowdfunding:finish-due';

    protected $description = '兜底：结算已到期仍处于众筹中的项目（补偿延迟任务丢失或未跑队列）';

    public function handle(CrowdfundingFinishService $finishService): int
    {
        CrowdfundingProduct::query()
            ->where('status', CrowdfundingProduct::STATUS_FUNDING)
            ->where('end_at', '<=', now())
            ->each(function (CrowdfundingProduct $cf) use ($finishService) {
                $finishService->finish($cf, null);
            });

        return self::SUCCESS;
    }
}
