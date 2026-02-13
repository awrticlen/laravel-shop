<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()->label('删除'),
        ];
    }

    public function afterSave(): void
    {
        $this->record->refresh();
        $skus = $this->record->skus;
        $minPrice = $skus->isEmpty() ? 0 : (float) $skus->min('price');
        $this->record->updateQuietly(['price' => $minPrice]);
    }
}
