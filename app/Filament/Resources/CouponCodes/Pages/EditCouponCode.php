<?php

namespace App\Filament\Resources\CouponCodes\Pages;

use App\Filament\Resources\CouponCodes\CouponCodeResource;
use App\Models\CouponCode;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCouponCode extends EditRecord
{
    protected static string $resource = CouponCodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (blank($data['code'] ?? null)) {
            $data['code'] = CouponCode::findAvailableCode();
        }

        return $data;
    }
}
