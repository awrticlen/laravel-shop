<?php

namespace App\Filament\Resources\Categories\Schemas;

use App\Models\Category;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
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
                        Radio::make('is_directory')
                            ->label('是否目录')
                            ->options([
                                1 => '是',
                                0 => '否',
                            ])
                            ->inline()
                            ->inlineLabel(false)
                            ->default(0)
                            ->required()
                            ->disabled(fn (string $operation): bool => $operation === 'edit'),
                        Select::make('parent_id')
                            ->label('父类目')
                            ->searchable()
                            ->getSearchResultsUsing(fn (string $search): array => Category::query()
                                ->where('is_directory', true)
                                ->where('name', 'like', "%{$search}%")
                                ->limit(20)
                                ->get()
                                ->mapWithKeys(fn (Category $category): array => [$category->id => $category->full_name])
                                ->toArray())
                            ->getOptionLabelUsing(fn ($value): ?string => $value
                                ? Category::query()->find($value)?->full_name
                                : null)
                            ->helperText('仅可选择目录类目作为父类目')
                            ->disabled(fn (string $operation): bool => $operation === 'edit'),
                        TextInput::make('level')
                            ->label('层级')
                            ->disabled()
                            ->dehydrated(false)
                            ->visible(fn (string $operation): bool => $operation === 'edit'),
                        TextInput::make('path')
                            ->label('类目路径')
                            ->disabled()
                            ->dehydrated(false)
                            ->visible(fn (string $operation): bool => $operation === 'edit'),
                    ])
                    ->columns(2),
            ]);
    }
}