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
                            ->nullable()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        Select::make('type')
                            ->label('类型')
                            ->options(CouponCode::$typeMap)
                            ->required()
                            ->default(CouponCode::TYPE_FIXED),

                        TextInput::make('value')
                            ->label('折扣')
                            ->required()
                            ->numeric()
                            ->rules(function ($get) {
                                if ($get('type') === CouponCode::TYPE_PERCENT) {
                                    return ['required', 'numeric', 'between:1,99'];
                                }

                                return ['required', 'numeric', 'min:0.01'];
                            }),

                        TextInput::make('min_amount')
                            ->label('最低金额')
                            ->required()
                            ->numeric()
                            ->minValue(0),

                        TextInput::make('total')
                            ->label('总量')
                            ->required()
                            ->numeric()
                            ->minValue(0),

                        TextInput::make('used')
                            ->label('已用')
                            ->numeric()
                            ->disabled(),

                        DateTimePicker::make('not_before')
                            ->label('开始时间')
                            ->nullable(),

                        DateTimePicker::make('not_after')
                            ->label('结束时间')
                            ->nullable(),

                        Toggle::make('enabled')
                            ->label('是否启用')
                            ->required()
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
    }
}
