<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Exceptions\InternalException;
use App\Exceptions\InvalidRequestException;
use App\Models\CrowdfundingProduct;
use App\Models\Order;
use App\Services\OrderService;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('订单信息')
                    ->schema([
                        TextEntry::make('no')
                            ->label('订单号'),
                        TextEntry::make('user.name')
                            ->label('买家'),
                        TextEntry::make('total_amount')
                            ->label('订单金额')
                            ->numeric(),
                        TextEntry::make('paid_at')
                            ->label('支付时间')
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('payment_method')
                            ->label('支付方式')
                            ->formatStateUsing(fn (?string $state): string => match ($state) {
                                'alipay' => '支付宝',
                                'wechat' => '微信',
                                'installment' => '分期付款',
                                default => (string) $state,
                            })
                            ->placeholder('-'),
                        TextEntry::make('payment_no')
                            ->label('支付渠道单号')
                            ->placeholder('-'),
                        TextEntry::make('address')
                            ->label('收货地址')
                            ->columnSpanFull(),
                        RepeatableEntry::make('items')
                            ->label('商品列表')
                            ->schema([
                                TextEntry::make('product.title')
                                    ->label('商品名称'),
                                TextEntry::make('amount')
                                    ->label('数量'),
                                TextEntry::make('price')
                                    ->label('单价')
                                    ->money('CNY'),
                            ])
                            ->columns(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('物流信息')
                    ->schema([
                        TextEntry::make('ship_status')
                            ->label('物流状态')
                            ->badge()
                            ->formatStateUsing(fn (?string $state): string => match ($state) {
                                'pending' => '待发货',
                                'delivered' => '已发货',
                                'received' => '已收货',
                                default => (string) $state,
                            })
                            ->color(fn (?string $state): string => match ($state) {
                                'pending' => 'gray',
                                'delivered' => 'success',
                                'received' => 'success',
                                default => 'gray',
                            }),
                        TextEntry::make('shipping_info')
                            ->label('物流公司 / 单号')
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
                            ->afterContent([
                                Action::make('ship')
                                    ->label('发货')
                                    ->button()
                                    ->color('success')
                                    ->visible(fn ($record): bool => filled($record->paid_at)
                                        && $record->ship_status === Order::SHIP_STATUS_PENDING
                                        && $record->refund_status !== Order::REFUND_STATUS_SUCCESS
                                        && (
                                            $record->type !== Order::TYPE_CROWDFUNDING
                                            || optional(optional($record->items->first())->product->crowdfunding)->status === CrowdfundingProduct::STATUS_SUCCESS
                                        ))
                                    ->form([
                                        Select::make('express_company')
                                            ->label('物流公司')
                                            ->options([
                                                'SF' => '顺丰',
                                                'YTO' => '圆通',
                                                'ZTO' => '中通',
                                                'YD' => '韵达',
                                                'STO' => '申通',
                                            ])
                                            ->required(),
                                        TextInput::make('express_no')
                                            ->label('物流单号')
                                            ->required()
                                            ->maxLength(50),
                                    ])
                                    ->action(function ($record, array $data): void {
                                        // 众筹订单仅允许在众筹成功后发货（后端兜底）
                                        if (
                                            $record->type === Order::TYPE_CROWDFUNDING
                                            && optional(optional($record->items->first())->product->crowdfunding)->status !== CrowdfundingProduct::STATUS_SUCCESS
                                        ) {
                                            throw new InvalidRequestException('众筹订单只能在众筹成功之后发货');
                                        }

                                        $record->update([
                                            'ship_status' => Order::SHIP_STATUS_DELIVERED,
                                            'ship_data' => [
                                                'express_company' => $data['express_company'],
                                                'express_no' => $data['express_no'],
                                            ],
                                        ]);

                                        Notification::make()
                                            ->title('发货成功')
                                            ->success()
                                            ->send();
                                    }),
                            ]),
                    ])
                    ->columns(2),
                Section::make('退款信息')
                    ->schema([
                        TextEntry::make('refund_summary')
                            ->label('退款状态')
                            ->state(function ($record): string {
                                $status = Order::$refundStatusMap[$record->refund_status] ?? (string) $record->refund_status;
                                $refundReason = $record->extra['refund_reason'] ?? '';
                                $disagreeReason = $record->extra['refund_disagree_reason'] ?? '';

                                if ($record->refund_status === Order::REFUND_STATUS_APPLIED) {
                                    return $status . ($refundReason ? '，理由：' . $refundReason : '');
                                }

                                if ($record->refund_status === Order::REFUND_STATUS_FAILED) {
                                    return $status . ($disagreeReason ? '，拒绝理由：' . $disagreeReason : '');
                                }

                                return $status;
                            })
                            ->columnSpanFull()
                            ->afterContent([
                                Action::make('agree_refund')
                                    ->label('同意退款')
                                    ->button()
                                    ->color('success')
                                    ->visible(fn ($record): bool => $record->refund_status === Order::REFUND_STATUS_APPLIED)
                                    ->requiresConfirmation()
                                    ->action(function ($record): void {
                                        try {
                                            app(OrderService::class)->refundOrder($record);
                                        } catch (InvalidRequestException | InternalException $e) {
                                            Notification::make()
                                                ->title('退款未成功')
                                                ->body($e->getMessage())
                                                ->danger()
                                                ->send();

                                            return;
                                        }

                                        $record->refresh();

                                        if ($record->refund_status === Order::REFUND_STATUS_FAILED) {
                                            Notification::make()
                                                ->title('支付宝退款失败')
                                                ->body(
                                                    '错误码：'.($record->extra['refund_failed_code'] ?? '未知')
                                                )
                                                ->danger()
                                                ->send();

                                            return;
                                        }

                                        Notification::make()
                                            ->title('已同意并完成退款')
                                            ->success()
                                            ->send();
                                    }),
                                Action::make('reject_refund')
                                    ->label('不同意退款')
                                    ->button()
                                    ->color('danger')
                                    ->visible(fn ($record): bool => $record->refund_status === Order::REFUND_STATUS_APPLIED)
                                    ->form([
                                        Textarea::make('reason')
                                            ->label('拒绝理由')
                                            ->required()
                                            ->rows(3),
                                    ])
                                    ->action(function ($record, array $data): void {
                                        $extra = $record->extra ?: [];
                                        $extra['refund_disagree_reason'] = $data['reason'];

                                        $record->update([
                                            'refund_status' => Order::REFUND_STATUS_FAILED,
                                            'extra' => $extra,
                                        ]);

                                        Notification::make()
                                            ->title('已拒绝退款')
                                            ->success()
                                            ->send();
                                    }),
                            ]),
                        TextEntry::make('refund_no')
                            ->label('退款单号')
                            ->placeholder('-'),
                        TextEntry::make('refund_reason')
                            ->label('退款理由')
                            ->state(fn ($record): string => (string) ($record->extra['refund_reason'] ?? '-'))
                            ->placeholder('-'),
                    ])
                    ->columns(2),
                Section::make('其他信息')
                    ->schema([
                        IconEntry::make('closed')
                            ->label('已关闭')
                            ->boolean(),
                        IconEntry::make('reviewed')
                            ->label('已评价')
                            ->boolean(),
                        TextEntry::make('remark')
                            ->label('备注')
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
                    ])
                    ->columns(2),
            ]);
    }
}
