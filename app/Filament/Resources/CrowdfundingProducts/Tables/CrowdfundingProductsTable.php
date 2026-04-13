<?php

namespace App\Filament\Resources\CrowdfundingProducts\Tables;

use App\Models\CrowdfundingProduct;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CrowdfundingProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['crowdfunding', 'category']))
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('title')
                    ->label('商品名称')
                    ->searchable(),
                TextColumn::make('on_sale')
                    ->label('已上架')
                    ->formatStateUsing(fn ($state): string => (bool) $state ? '是' : '否')
                    ->badge()
                    ->color(fn ($state): string => (bool) $state ? 'success' : 'gray'),
                TextColumn::make('price')
                    ->label('价格')
                    ->money('CNY')
                    ->sortable(),
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
            ])
            ->filters([])
            ->recordActions([
                EditAction::make()->label('编辑'),
            ])
            ->toolbarActions([]);
    }
}
