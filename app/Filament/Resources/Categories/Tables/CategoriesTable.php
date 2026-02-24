<?php

namespace App\Filament\Resources\Categories\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
        ->columns([
            TextColumn::make('id')
                ->label('ID')
                ->sortable(),
            TextColumn::make('name')
                ->label('分类名称')
                ->searchable()
                ->sortable(),
            TextColumn::make('slug')
                ->label('别名')
                ->searchable(),
            ImageColumn::make('image')
                ->label('图片'),
            IconColumn::make('is_active')
                ->label('启用')
                ->boolean(),
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
