<?php

namespace App\Filament\Resources\SeckillProducts\Schemas;

use App\Filament\Resources\Products\Schemas\CommonProductForm;
use App\Models\Product;
use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Schema;

class SeckillProductsForm
{
    public static function configure(Schema $schema): Schema
    {
        return CommonProductForm::configure(
            $schema,
            extraBasicFields: [
                DateTimePicker::make('sk_start_at')
                    ->label('秒杀开始时间')
                    ->required()
                    ->seconds(false),
                DateTimePicker::make('sk_end_at')
                    ->label('秒杀结束时间')
                    ->required()
                    ->after('sk_start_at')
                    ->seconds(false),
            ],
            productType: Product::TYPE_SECKILL
        );
    }
}
