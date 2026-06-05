<?php

namespace App\Filament\Resources\SeckillProducts\Pages;

use App\Filament\Resources\SeckillProducts\SeckillProductsResource;
use App\Models\Product;
use App\Support\Products\SyncProductMinPrice;
use App\Support\Products\SyncProductToElasticsearch;
use App\Support\Products\SyncSeckillSkuStockToRedis;
use Filament\Resources\Pages\CreateRecord;

class CreateSeckillProduct extends CreateRecord
{
    protected static string $resource = SeckillProductsResource::class;

    /** @var array{start_at: mixed, end_at: mixed}|null */
    protected ?array $pendingSeckill = null;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->pendingSeckill = [
            'start_at' => $data['sk_start_at'] ?? now(),
            'end_at' => $data['sk_end_at'] ?? now(),
        ];
        unset($data['sk_start_at'], $data['sk_end_at']);

        $data['type'] = Product::TYPE_SECKILL;

        return $data;
    }

    protected function afterCreate(): void
    {
        if ($this->pendingSeckill !== null) {
            $this->record->seckill()->create($this->pendingSeckill);
        }

        SyncProductMinPrice::run($this->record);
        SyncProductToElasticsearch::run($this->record);
        SyncSeckillSkuStockToRedis::run($this->record);
    }
}
