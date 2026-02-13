<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
        ->components([
            TextInput::make('title')
                ->label('商品名称')
                ->required(),
            Textarea::make('description')
                ->label('商品描述')
                ->required()
                ->columnSpanFull(),
            FileUpload::make('image')
                ->label('图片')
                ->image()
                ->required(),
            Toggle::make('on_sale')
                ->label('已上架')
                ->required(),
            TextInput::make('rating')
                ->label('评分')
                ->required()
                ->numeric()
                ->default(5),
            TextInput::make('sold_count')
                ->label('销量')
                ->required()
                ->numeric()
                ->default(0),
            TextInput::make('review_count')
                ->label('评论数')
                ->required()
                ->numeric()
                ->default(0),
            TextInput::make('price')
                ->label('价格')
                ->required()
                ->numeric()
                ->prefix('¥'),
        ]);
    }
}
