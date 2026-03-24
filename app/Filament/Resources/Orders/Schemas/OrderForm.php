<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('no')
                    ->required(),
                TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                Textarea::make('address')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('total_amount')
                    ->required()
                    ->numeric(),
                Textarea::make('remark')
                    ->columnSpanFull(),
                DateTimePicker::make('paid_at'),
                TextInput::make('payment_method'),
                TextInput::make('payment_no'),
                TextInput::make('refund_status')
                    ->required()
                    ->default('pending'),
                TextInput::make('refund_no'),
                Toggle::make('closed')
                    ->required(),
                Toggle::make('reviewed')
                    ->required(),
                TextInput::make('ship_status')
                    ->required()
                    ->default('pending'),
                Textarea::make('ship_data')
                    ->columnSpanFull(),
                Textarea::make('extra')
                    ->columnSpanFull(),
            ]);
    }
}
