<?php

namespace App\Filament\Resources\CrowdfundingProducts\Pages;

use App\Filament\Resources\CrowdfundingProducts\CrowdfundingProductsResource;
use App\Support\Products\SyncProductMinPrice;
use App\Support\Products\SyncProductToElasticsearch;
use Filament\Resources\Pages\EditRecord;

class EditCrowdfundingProduct extends EditRecord
{
    protected static string $resource = CrowdfundingProductsResource::class;

    /** @var array{target_amount: float|null, end_at: mixed}|null */
    protected ?array $pendingCrowdfunding = null;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $this->record->loadMissing('crowdfunding');
        if ($this->record->crowdfunding) {
            $data['cf_target_amount'] = $this->record->crowdfunding->target_amount;
            $data['cf_end_at'] = $this->record->crowdfunding->end_at;
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->pendingCrowdfunding = [
            'target_amount' => isset($data['cf_target_amount']) ? (float) $data['cf_target_amount'] : null,
            'end_at' => $data['cf_end_at'] ?? null,
        ];
        unset($data['cf_target_amount'], $data['cf_end_at']);

        return $data;
    }

    protected function afterSave(): void
    {
        if ($this->pendingCrowdfunding !== null
            && $this->pendingCrowdfunding['target_amount'] !== null
            && $this->pendingCrowdfunding['end_at'] !== null) {
            $this->record->crowdfunding()->updateOrCreate(
                ['product_id' => $this->record->getKey()],
                [
                    'target_amount' => $this->pendingCrowdfunding['target_amount'],
                    'end_at' => $this->pendingCrowdfunding['end_at'],
                ]
            );
            $this->record->load('crowdfunding');
            $this->record->crowdfunding?->scheduleDelayedFinish();
        }

        SyncProductMinPrice::run($this->record);
        SyncProductToElasticsearch::run($this->record);
    }
}
