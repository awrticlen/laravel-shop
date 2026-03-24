<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class OrderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('no')
                    ->label('订单号'),
                TextEntry::make('user.name')
                    ->label('用户'),
                TextEntry::make('address')
                    ->label('收货地址')
                    ->columnSpanFull(),
                TextEntry::make('total_amount')
                    ->label('订单金额')
                    ->numeric(),
                TextEntry::make('remark')
                    ->label('备注')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('paid_at')
                    ->label('支付时间')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('payment_method')
                    ->label('支付方式')
                    ->placeholder('-'),
                TextEntry::make('payment_no')
                    ->label('支付单号')
                    ->placeholder('-'),
                TextEntry::make('refund_status')
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
                    }),
                TextEntry::make('refund_no')
                    ->label('退款单号')
                    ->placeholder('-'),
                IconEntry::make('closed')
                    ->label('已关闭')
                    ->boolean(),
                IconEntry::make('reviewed')
                    ->label('已评价')
                    ->boolean(),
                TextEntry::make('ship_status')
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
                    }),
                TextEntry::make('shipping_info')
                    ->label('物流信息')
                    ->state(function ($record): string {
                        $state = $record->ship_data;

                        if (is_string($state) && $state !== '') {
                            $decoded = json_decode($state, true);
                            if (json_last_error() === JSON_ERROR_NONE) {
                                $state = $decoded;
                            }
                        }

                        if (!is_array($state) || empty($state)) {
                            return '-';
                        }

                        $companyMap = [
                            'SF' => '顺丰',
                            'YTO' => '圆通',
                            'ZTO' => '中通',
                            'YD' => '韵达',
                            'STO' => '申通',
                        ];

                        $companyCode = $state['express_company'] ?? '';
                        $companyName = $companyMap[$companyCode] ?? ($companyCode ?: '-');
                        $expressNo = $state['express_no'] ?? '-';

                        return $companyName . ' / ' . $expressNo;
                    })
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('extra')
                    ->label('扩展信息')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->label('创建时间')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->label('更新时间')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
