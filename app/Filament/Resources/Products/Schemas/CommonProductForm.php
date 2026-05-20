<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Models\Category;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CommonProductForm
{
    /**
     * @param  array<Component|Field>  $extraBasicFields
     */
    public static function configure(Schema $schema, array $extraBasicFields = [], ?string $productType = null): Schema
    {
        $baseFields = [
            Select::make('category_id')
                ->label('类目')
                ->searchable()
                ->preload()
                ->getSearchResultsUsing(fn (string $search): array => Category::query()
                    ->where('is_directory', false)
                    ->where('name', 'like', "%{$search}%")
                    ->limit(20)
                    ->get()
                    ->mapWithKeys(fn (Category $category): array => [$category->id => $category->full_name])
                    ->toArray())
                ->getOptionLabelUsing(fn ($value): ?string => $value
                    ? Category::query()->find($value)?->full_name
                    : null)
                ->helperText('仅选择叶子类目；可输入名称搜索')
                ->nullable(),
            TextInput::make('title')
                ->label('商品名称')
                ->required(),
            TextInput::make('long_title')
                ->label('商品长标题')
                ->required()
                ->columnSpanFull(),
            Textarea::make('description')
                ->label('商品描述')
                ->required()
                ->columnSpanFull(),
            FileUpload::make('image')
                ->label('封面图片')
                ->image()
                ->disk('public')
                ->directory('products')
                ->visibility('public')
                ->required(fn (string $operation): bool => $operation === 'create')
                ->dehydrated(fn ($state) => filled($state)),
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
        ];

        if ($productType !== null) {
            $baseFields[] = Hidden::make('type')
                ->default($productType)
                ->dehydrated(true);
        }

        return $schema
            ->columns(1)
            ->components([
                Section::make('基本信息')
                    ->description('商品展示与上架信息')
                    ->schema([
                        ...$baseFields,
                        ...$extraBasicFields,
                    ])
                    ->columns(2)
                    ->columnSpanFull()
                    ->compact(),
                Section::make('商品 SKU')
                    ->description('规格、价格与库存')
                    ->schema([
                        Repeater::make('skus')
                            ->relationship()
                            ->label('SKU 列表')
                            ->schema([
                                TextInput::make('title')
                                    ->label('SKU 名称')
                                    ->required(),
                                TextInput::make('description')
                                    ->label('SKU 描述')
                                    ->required(),
                                TextInput::make('price')
                                    ->label('价格')
                                    ->required()
                                    ->numeric()
                                    ->minValue(0.01)
                                    ->step(0.01)
                                    ->prefix('¥'),
                                TextInput::make('stock')
                                    ->label('库存')
                                    ->required()
                                    ->numeric()
                                    ->integer()
                                    ->minValue(0),
                            ])
                            ->minItems(1)
                            ->defaultItems(1)
                            ->addActionLabel('添加 SKU')
                            ->columns(4)
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull()
                    ->compact(),
                Section::make('商品属性')
                    ->description('用于前台筛选的参数')
                    ->schema([
                        Repeater::make('properties')
                            ->relationship()
                            ->label('商品属性')
                            ->schema([
                                TextInput::make('name')
                                    ->label('属性名')
                                    ->required(),
                                TextInput::make('value')
                                    ->label('属性值')
                                    ->required(),
                            ])
                            ->addActionLabel('添加属性')
                            ->columns(2)
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull()
                    ->compact(),
            ]);
    }
}

