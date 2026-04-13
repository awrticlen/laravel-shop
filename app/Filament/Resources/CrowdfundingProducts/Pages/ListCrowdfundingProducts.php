<?php

namespace App\Filament\Resources\CrowdfundingProducts\Pages;

use App\Filament\Resources\CrowdfundingProducts\CrowdfundingProductsResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCrowdfundingProducts extends ListRecords
{
    protected static string $resource = CrowdfundingProductsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('新增'),
        ];
    }
}
