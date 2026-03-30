<?php

namespace App\Filament\Resources\CouponCodes\Pages;

use App\Filament\Resources\CouponCodes\CouponCodeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCouponCode extends CreateRecord
{
    protected static string $resource = CouponCodeResource::class;
}
