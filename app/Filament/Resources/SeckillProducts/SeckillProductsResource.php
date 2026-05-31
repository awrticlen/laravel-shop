<?php

namespace App\Filament\Resources\SeckillProducts;

use App\Filament\Resources\SeckillProducts\Pages\CreateSeckillProduct;
use App\Filament\Resources\SeckillProducts\Pages\EditSeckillProduct;
use App\Filament\Resources\SeckillProducts\Pages\ListSeckillProducts;
use App\Filament\Resources\SeckillProducts\Schemas\SeckillProductsForm;
use App\Filament\Resources\SeckillProducts\Tables\SeckillProductsTable;
use App\Models\Product;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SeckillProductsResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = '秒杀商品';

    protected static string|\UnitEnum|null $navigationGroup = '商品管理';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBolt;

    public static function getModelLabel(): string
    {
        return '秒杀商品';
    }

    public static function getPluralModelLabel(): string
    {
        return '秒杀商品';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('type', Product::TYPE_SECKILL);
    }

    public static function form(Schema $schema): Schema
    {
        return SeckillProductsForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SeckillProductsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSeckillProducts::route('/'),
            'create' => CreateSeckillProduct::route('/create'),
            'edit' => EditSeckillProduct::route('/{record}/edit'),
        ];
    }
}
