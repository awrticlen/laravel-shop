<?php

namespace App\Filament\Resources\CouponCodes\Schemas;

use App\Models\CouponCode;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CouponCodeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('优惠券信息')
                    ->schema([
                        TextInput::make('name')
                            ->label('名称')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('code')
                            ->label('优惠码')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        Select::make('type')
                            ->label('类型')
                            ->options(CouponCode::$typeMap)
                            ->required(),

                        TextInput::make('value')
                            ->label('折扣')
                            ->required()
                            ->numeric(),

                        TextInput::make('min_amount')
                            ->label('最低金额')
                            ->required()
                            ->numeric(),

                        TextInput::make('total')
                            ->label('总量')
                            ->required()
                            ->numeric(),

                        TextInput::make('used')
                            ->label('已用')
                            ->numeric()
                            ->disabled(),

                        DateTimePicker::make('not_before')
                            ->label('开始时间'),

                        DateTimePicker::make('not_after')
                            ->label('结束时间'),

                        Toggle::make('enabled')
                            ->label('是否启用')
                            ->required()
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
    }
}
