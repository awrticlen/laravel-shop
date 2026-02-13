<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('姓名')
                    ->required(),

                TextInput::make('email')
                    ->label('邮箱')
                    ->email()
                    ->required(),

                DateTimePicker::make('email_verified_at')
                    ->label('邮箱验证时间'),

                TextInput::make('password')
                    ->label('密码')
                    ->password()
                    // 创建时必填，编辑时可留空（避免更新用户资料时被强制改密码）
                    ->required(fn ($record) => $record === null)
                    ->dehydrated(fn ($state) => filled($state))
                    ->confirmed(),

                TextInput::make('password_confirmation')
                    ->label('确认密码')
                    ->password()
                    ->required(fn ($record) => $record === null)
                    ->dehydrated(false)
                    ->visible(fn ($record) => $record === null),

                Select::make('roles')
                    ->label('角色')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable(),
            ]);
    }
}
