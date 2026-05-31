<?php

namespace App\Filament\Resources\SeckillProducts\Pages;

use App\Filament\Resources\SeckillProducts\SeckillProductsResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSeckillProducts extends ListRecords
{
    protected static string $resource = SeckillProductsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('新增'),
        ];
    }
}
