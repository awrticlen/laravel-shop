<?php

namespace App\Filament\Resources\Orders\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('no')
                    ->label('订单号')
                    ->copyable()
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label('用户')
                    ->sortable(),
                TextColumn::make('total_amount')
                    ->label('总金额')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('paid_at')
                    ->label('支付时间')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('payment_method')
                    ->label('支付方式')
                    ->searchable(),
                TextColumn::make('payment_no')
                    ->label('支付单号')
                    ->searchable(),
                TextColumn::make('refund_status')
                    ->label('退款状态')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'pending' => '待处理',
                        'applied' => '已申请',
                        'success' => '退款成功',
                        'failed' => '退款失败',
                        default => (string) $state,
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'pending' => 'gray',
                        'applied' => 'warning',
                        'success' => 'success',
                        'failed' => 'danger',
                        default => 'gray',
                    })
                    ->searchable(),
                TextColumn::make('refund_no')
                    ->label('退款单号')
                    ->searchable(),
                IconColumn::make('closed')
                    ->label('已关闭')
                    ->boolean(),
                IconColumn::make('reviewed')
                    ->label('已评价')
                    ->boolean(),
                TextColumn::make('ship_status')
                    ->label('发货状态')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'pending' => '待发货',
                        'delivered' => '已发货',
                        default => (string) $state,
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'pending' => 'gray',
                        'delivered' => 'success',
                        default => 'gray',
                    })
                    ->searchable(),
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
                ViewAction::make(),
            ])
            ->toolbarActions([]);
    }
}
