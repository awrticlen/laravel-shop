<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('分类信息')
                    ->schema([
                        TextInput::make('name')
                            ->label('分类名称')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('slug')
                            ->label('别名')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        FileUpload::make('image')
                            ->label('分类图片')
                            ->image()
                            ->disk('public')
                            ->directory('categories')
                            ->visibility('public'),
                        Toggle::make('is_active')
                            ->label('启用')
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
    }
}