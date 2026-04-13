<?php

namespace App\Filament\Resources\CrowdfundingProducts\Pages;

use App\Filament\Resources\CrowdfundingProducts\CrowdfundingProductsResource;
use App\Models\CrowdfundingProduct;
use App\Models\Product;
use Filament\Resources\Pages\CreateRecord;

class CreateCrowdfundingProduct extends CreateRecord
{
    protected static string $resource = CrowdfundingProductsResource::class;

    /** @var array{target_amount: float, end_at: mixed}|null */
    protected ?array $pendingCrowdfunding = null;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->pendingCrowdfunding = [
            'target_amount' => (float) ($data['cf_target_amount'] ?? 0),
            'end_at' => $data['cf_end_at'] ?? now(),
        ];
        unset($data['cf_target_amount'], $data['cf_end_at']);

        $data['type'] = Product::TYPE_CROWDFUNDING;

        return $data;
    }

    protected function afterCreate(): void
    {
        if ($this->pendingCrowdfunding !== null) {
            $this->record->crowdfunding()->create([
                'target_amount' => $this->pendingCrowdfunding['target_amount'],
                'end_at' => $this->pendingCrowdfunding['end_at'],
                'total_amount' => 0,
                'user_count' => 0,
                'status' => CrowdfundingProduct::STATUS_FUNDING,
            ]);
        }

        $this->record->refresh();
        $skus = $this->record->skus;
        $minPrice = $skus->isEmpty() ? 0 : (float) $skus->min('price');
        $this->record->updateQuietly(['price' => $minPrice]);
    }
}
