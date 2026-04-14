<?php

namespace App\Filament\Resources\CrowdfundingProducts\Schemas;

use App\Filament\Resources\Products\Schemas\CommonProductForm;
use App\Models\Product;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CrowdfundingProductsForm
{
    public static function configure(Schema $schema): Schema
    {
        return CommonProductForm::configure(
            $schema,
            extraBasicFields: [
                TextInput::make('cf_target_amount')
                    ->label('众筹目标金额')
                    ->numeric()
                    ->required()
                    ->minValue(0.01)
                    ->step(0.01)
                    ->prefix('¥'),
                DateTimePicker::make('cf_end_at')
                    ->label('众筹结束时间')
                    ->required()
                    ->seconds(false),
            ],
            productType: Product::TYPE_CROWDFUNDING
        );
    }
}
