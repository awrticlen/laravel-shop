<?php

namespace App\Filament\Resources\CouponCodes\Pages;

use App\Filament\Resources\CouponCodes\CouponCodeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCouponCodes extends ListRecords
{
    protected static string $resource = CouponCodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
