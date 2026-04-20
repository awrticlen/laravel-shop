<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\OrderRefundService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class RefundCrowdfundingOrderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $orderId) {}

    public function handle(OrderRefundService $refunds): void
    {
        $order = Order::query()->find($this->orderId);

        if (! $order) {
            return;
        }

        try {
            $refunds->refundPaidOrderPending($order);
        } catch (Throwable $e) {
            Log::error('众筹失败自动退款失败', [
                'order_id' => $this->orderId,
                'message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
