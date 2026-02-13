<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    public function afterCreate(): void
    {
        $this->record->refresh();
        $skus = $this->record->skus;
        $minPrice = $skus->isEmpty() ? 0 : (float) $skus->min('price');
        $this->record->updateQuietly(['price' => $minPrice]);
    }
}
