<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use App\Models\Product;
use App\Support\Products\SyncProductMinPrice;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['type'] = Product::TYPE_NORMAL;

        return $data;
    }

    public function afterCreate(): void
    {
        SyncProductMinPrice::run($this->record);
    }
}
