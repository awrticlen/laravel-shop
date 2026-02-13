<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('基本信息')
                    ->schema([
                        TextInput::make('title')
                            ->label('商品名称')
                            ->required()
                            ->rules(['required']),
                        Textarea::make('description')
                            ->label('商品描述')
                            ->required()
                            ->rules(['required'])
                            ->columnSpanFull(),
                        FileUpload::make('image')
                            ->label('商品图片')
                            ->image()
                            ->disk('public')
                            ->directory('products')
                            ->visibility('public')
                            ->required(),
                        Radio::make('on_sale')
                            ->label('上架')
                            ->options(['1' => '是', '0' => '否'])
                            ->default('0'),
                        TextInput::make('rating')
                            ->label('评分')
                            ->numeric()
                            ->default(5),
                        TextInput::make('sold_count')
                            ->label('销量')
                            ->numeric()
                            ->default(0),
                        TextInput::make('review_count')
                            ->label('评论数')
                            ->numeric()
                            ->default(0),
                    ])
                    ->columns(2),
                Section::make('商品 SKU')
                    ->schema([
                        Repeater::make('skus')
                            ->relationship()
                            ->label('SKU 列表')
                            ->schema([
                                TextInput::make('title')
                                    ->label('SKU 名称')
                                    ->required()
                                    ->rules(['required']),
                                TextInput::make('description')
                                    ->label('SKU 描述')
                                    ->required()
                                    ->rules(['required']),
                                TextInput::make('price')
                                    ->label('价格')
                                    ->required()
                                    ->rules(['required', 'numeric', 'min:0.01'])
                                    ->numeric()
                                    ->minValue(0.01)
                                    ->step(0.01)
                                    ->prefix('¥'),
                                TextInput::make('stock')
                                    ->label('库存')
                                    ->required()
                                    ->rules(['required', 'integer', 'min:0'])
                                    ->numeric()
                                    ->integer()
                                    ->minValue(0),
                            ])
                            ->minItems(1)
                            ->defaultItems(1)
                            ->addActionLabel('添加 SKU')
                            ->columns(2)
                            ->collapsible()
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
