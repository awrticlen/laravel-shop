<?php

namespace App\Filament\Resources\CouponCodes\Pages;

use App\Filament\Resources\CouponCodes\CouponCodeResource;
use App\Models\CouponCode;
use Filament\Resources\Pages\CreateRecord;

class CreateCouponCode extends CreateRecord
{
    protected static string $resource = CouponCodeResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (blank($data['code'] ?? null)) {
            $data['code'] = CouponCode::findAvailableCode();
        }

        return $data;
    }
}
