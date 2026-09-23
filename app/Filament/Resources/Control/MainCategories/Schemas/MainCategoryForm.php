<?php

namespace App\Filament\Resources\Control\MainCategories\Schemas;

use App\Filament\Resources\Control\Schemas\BranchSelect;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Unique;

class MainCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columns(5)
                    ->columnSpanFull()
                    ->schema([
                        Grid::make()
                            ->columns(1)
                            ->columnSpan(3)
                            ->inlineLabel()
                            ->schema([
                                BranchSelect::make(),
                                static::nameInput('cat_name', 'global.cat_name'),
                                static::nameInput('cat_name_kh', 'global.cat_name_kh'),
                            ]),
                    ]),
            ]);
    }

    protected static function nameInput(string $column, string $label): TextInput
    {
        return TextInput::make($column)
            ->required()
            ->maxLength(255)
            ->label(__($label))
            ->unique(
                table: 'main_categories',
                column: $column,
                ignoreRecord: true,
                modifyRuleUsing: fn (Unique $rule, Get $get) => $rule->where('branch_id', $get('branch_id')),
            )
            ->validationMessages([
                'required' => __('validation.required', ['attribute' => __($label)]),
                'unique' => __('validation.unique', ['attribute' => __($label)]),
            ]);
    }
}
