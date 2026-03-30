<?php

namespace App\Filament\Resources\CouponCodes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CouponCodesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('name')
                    ->label('名称')
                    ->searchable(),

                TextColumn::make('code')
                    ->label('优惠码')
                    ->searchable(),

                TextColumn::make('description')
                    ->label('描述')
                    ->searchable(),

                TextColumn::make('usage')
                    ->label('用量')
                    ->searchable(),

                TextColumn::make('enabled')
                    ->label('是否启用')
                    ->badge()
                    ->formatStateUsing(fn (?bool $state): string => $state ? '是' : '否')
                    ->color(fn (?bool $state): string => $state ? 'success' : 'danger'),

                TextColumn::make('created_at')
                    ->label('创建时间')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
