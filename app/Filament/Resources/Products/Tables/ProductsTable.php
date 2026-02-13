<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
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
        TextColumn::make('rating')
            ->label('评分')
            ->numeric()
            ->sortable(),
        TextColumn::make('sold_count')
            ->label('销量')
            ->numeric()
            ->sortable(),
        TextColumn::make('review_count')
            ->label('评论数')
            ->numeric()
            ->sortable(),
        TextColumn::make('created_at')
            ->label('创建时间')
            ->dateTime()
            ->sortable()
            ->toggleable(isToggledHiddenByDefault: true),
        TextColumn::make('updated_at')
            ->label('更新时间')
            ->dateTime()
            ->sortable()
            ->toggleable(isToggledHiddenByDefault: true),
    ])
    ->filters([
        //
    ])
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
