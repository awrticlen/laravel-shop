<?php

namespace App\Filament\Resources\SeckillProducts\Pages;

use App\Filament\Resources\SeckillProducts\SeckillProductsResource;
use App\Models\Product;
use App\Support\Products\SyncProductMinPrice;
use App\Support\Products\SyncProductToElasticsearch;
use Filament\Resources\Pages\EditRecord;

class EditSeckillProduct extends EditRecord
{
    protected static string $resource = SeckillProductsResource::class;

    /** @var array{start_at: mixed|null, end_at: mixed|null}|null */
    protected ?array $pendingSeckill = null;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $this->record->loadMissing('seckill');
        if ($this->record->seckill) {
            $data['sk_start_at'] = $this->record->seckill->start_at;
            $data['sk_end_at'] = $this->record->seckill->end_at;
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->pendingSeckill = [
            'start_at' => $data['sk_start_at'] ?? null,
            'end_at' => $data['sk_end_at'] ?? null,
        ];
        unset($data['sk_start_at'], $data['sk_end_at']);

        if (blank($data['image'] ?? null)) {
            $data['image'] = $this->record->image;
        }

        $data['type'] = $data['type'] ?? $this->record->type ?? Product::TYPE_SECKILL;

        return $data;
    }

    protected function afterSave(): void
    {
        if ($this->pendingSeckill !== null
            && $this->pendingSeckill['start_at'] !== null
            && $this->pendingSeckill['end_at'] !== null) {
            $this->record->seckill()->updateOrCreate(
                ['product_id' => $this->record->getKey()],
                [
                    'start_at' => $this->pendingSeckill['start_at'],
                    'end_at' => $this->pendingSeckill['end_at'],
                ]
            );
        }

        SyncProductMinPrice::run($this->record);
        SyncProductToElasticsearch::run($this->record);
    }
}
