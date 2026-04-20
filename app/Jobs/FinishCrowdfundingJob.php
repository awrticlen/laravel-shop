<?php

namespace App\Jobs;

use App\Models\CrowdfundingProduct;
use App\Services\CrowdfundingFinishService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FinishCrowdfundingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $crowdfundingProductId,
        public int $expectedVersion
    ) {}

    public function handle(CrowdfundingFinishService $finishService): void
    {
        $cf = CrowdfundingProduct::query()->find($this->crowdfundingProductId);

        if (! $cf) {
            return;
        }

        $finishService->finish($cf, $this->expectedVersion);
    }
}
