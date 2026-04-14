<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CommonProductsTable
{
    /**
     * @param  array<TextColumn|IconColumn>  $extraColumns
     */
    public static function configure(
        Table $table,
        array $extraColumns = [],
        array $withRelations = [],
        bool $enableDeleteAction = true,
        bool $enableBulkDeleteAction = true,
    ): Table {
        $recordActions = [
            EditAction::make()->label('编辑'),
        ];

        if ($enableDeleteAction) {
            $recordActions[] = DeleteAction::make()->label('删除');
        }

        $toolbarActions = [];
        if ($enableBulkDeleteAction) {
            $toolbarActions[] = BulkActionGroup::make([
                DeleteBulkAction::make()->label('批量删除'),
            ]);
        }

        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(array_merge(['category'], $withRelations)))
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('title')
                    ->label('商品名称')
                    ->searchable(),
                TextColumn::make('category.full_name')
                    ->label('类目')
                    ->placeholder('—')
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('category', function (Builder $q) use ($search) {
                            $q->where('name', 'like', "%{$search}%");
                        });
                    }),
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
                ...$extraColumns,
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
            ->filters([])
            ->recordActions($recordActions)
            ->toolbarActions($toolbarActions);
    }
}

