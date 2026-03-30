<?php

namespace App\Filament\Resources\CouponCodes;

use App\Filament\Resources\CouponCodes\Pages\CreateCouponCode;
use App\Filament\Resources\CouponCodes\Pages\EditCouponCode;
use App\Filament\Resources\CouponCodes\Pages\ListCouponCodes;
use App\Filament\Resources\CouponCodes\Schemas\CouponCodeForm;
use App\Filament\Resources\CouponCodes\Tables\CouponCodesTable;
use App\Models\CouponCode;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CouponCodeResource extends Resource
{
    protected static ?string $model = CouponCode::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static ?string $navigationLabel = '优惠券';

    // 左侧菜单分组名
    protected static string|\UnitEnum|null $navigationGroup = '优惠券管理';

    protected static ?string $recordTitleAttribute = 'name';
    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return CouponCodeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CouponCodesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCouponCodes::route('/'),
            'create' => CreateCouponCode::route('/create'),
            'edit' => EditCouponCode::route('/{record}/edit'),
        ];
    }
}
