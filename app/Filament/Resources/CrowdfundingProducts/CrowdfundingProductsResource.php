<?php

namespace App\Filament\Resources\CrowdfundingProducts;

use App\Filament\Resources\CrowdfundingProducts\Pages\CreateCrowdfundingProduct;
use App\Filament\Resources\CrowdfundingProducts\Pages\EditCrowdfundingProduct;
use App\Filament\Resources\CrowdfundingProducts\Pages\ListCrowdfundingProducts;
use App\Filament\Resources\CrowdfundingProducts\Schemas\CrowdfundingProductsForm;
use App\Filament\Resources\CrowdfundingProducts\Tables\CrowdfundingProductsTable;
use App\Models\Product;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CrowdfundingProductsResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = '众筹商品';

    protected static string|\UnitEnum|null $navigationGroup = '商品管理';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCurrencyDollar;

    public static function getModelLabel(): string
    {
        return '众筹商品';
    }

    public static function getPluralModelLabel(): string
    {
        return '众筹商品';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('type', Product::TYPE_CROWDFUNDING);
    }

    public static function form(Schema $schema): Schema
    {
        return CrowdfundingProductsForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CrowdfundingProductsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCrowdfundingProducts::route('/'),
            'create' => CreateCrowdfundingProduct::route('/create'),
            'edit' => EditCrowdfundingProduct::route('/{record}/edit'),
        ];
    }
}
