<?php

namespace App\Filament\Resources\Categories\Tables;

use App\Models\Category;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('parent'))
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('name')
                    ->label('名称')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('level')
                    ->label('层级')
                    ->sortable(),
                TextColumn::make('is_directory')
                    ->label('是否目录')
                    ->formatStateUsing(fn ($state): string => (bool) $state ? '是' : '否')
                    ->badge()
                    ->color(fn ($state): string => (bool) $state ? 'success' : 'gray'),
                TextColumn::make('path')
                    ->label('类目路径')
                    ->searchable(),
                TextColumn::make('parent.full_name')
                    ->label('父类目')
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
