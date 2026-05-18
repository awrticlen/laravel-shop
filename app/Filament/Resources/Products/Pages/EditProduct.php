<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
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

    public function afterSave(): void
    {
        SyncProductMinPrice::run($this->record);
        SyncProductToElasticsearch::run($this->record);
    }
}
