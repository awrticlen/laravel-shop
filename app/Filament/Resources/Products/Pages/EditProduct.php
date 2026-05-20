<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use App\Models\Product;
use App\Support\Products\SyncProductMinPrice;
use App\Support\Products\SyncProductToElasticsearch;
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

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (blank($data['image'] ?? null)) {
            $data['image'] = $this->record->image;
        }

        $data['type'] = $data['type'] ?? $this->record->type ?? Product::TYPE_NORMAL;

        return $data;
    }

    public function afterSave(): void
    {
        SyncProductMinPrice::run($this->record);
        SyncProductToElasticsearch::run($this->record);
    }
}
