<?php

namespace App\Filament\Resources\SeckillProducts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SeckillProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('seckill'))
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('title')
                    ->label('商品名称')
                    ->searchable(),
                IconColumn::make('on_sale')
                    ->label('已上架')
                    ->boolean(),
                TextColumn::make('price')
                    ->label('价格')
                    ->money('CNY')
                    ->sortable(),
                TextColumn::make('seckill.start_at')
                    ->label('开始时间')
                    ->dateTime()
                    ->placeholder('—'),
                TextColumn::make('seckill.end_at')
                    ->label('结束时间')
                    ->dateTime()
                    ->placeholder('—'),
                TextColumn::make('sold_count')
                    ->label('销量')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([])
            ->recordActions([
                EditAction::make()->label('编辑'),
                DeleteAction::make()->label('删除'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('批量删除'),
                ]),
            ]);
    }
}
