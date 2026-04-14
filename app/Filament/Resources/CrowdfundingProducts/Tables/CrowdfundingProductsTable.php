<?php

namespace App\Filament\Resources\CrowdfundingProducts\Tables;

use App\Filament\Resources\Products\Tables\CommonProductsTable;
use App\Models\CrowdfundingProduct;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CrowdfundingProductsTable
{
    public static function configure(Table $table): Table
    {
        return CommonProductsTable::configure(
            $table,
            extraColumns: [
                TextColumn::make('crowdfunding.target_amount')
                    ->label('目标金额')
                    ->money('CNY')
                    ->placeholder('—'),
                TextColumn::make('crowdfunding.end_at')
                    ->label('结束时间')
                    ->dateTime()
                    ->placeholder('—'),
                TextColumn::make('crowdfunding.total_amount')
                    ->label('目前金额')
                    ->money('CNY')
                    ->placeholder('—'),
                TextColumn::make('crowdfunding.status')
                    ->label('状态')
                    ->formatStateUsing(fn (?string $state): string => $state
                        ? (CrowdfundingProduct::$statusMap[$state] ?? $state)
                        : '—'),
            ],
            withRelations: ['crowdfunding'],
            enableDeleteAction: false,
            enableBulkDeleteAction: false,
        );
    }
}
