<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use App\Models\Order;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('ship')
                ->label('发货')
                ->color('success')
                ->visible(fn (): bool => filled($this->record->paid_at) && $this->record->ship_status === Order::SHIP_STATUS_PENDING)
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
                ->action(function (array $data): void {
                    $this->record->update([
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
            EditAction::make(),
        ];
    }
}
