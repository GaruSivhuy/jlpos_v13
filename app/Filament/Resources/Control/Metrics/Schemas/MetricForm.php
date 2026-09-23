<?php

namespace App\Filament\Resources\Control\Metrics\Schemas;

use App\Filament\Resources\Control\Schemas\BranchSelect;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Unique;

class MetricForm
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
                                static::nameInput('name', 'global.mname'),
                                static::nameInput('name_kh', 'global.mname_kh'),
                                TextInput::make('qty')
                                    ->required()
                                    ->integer()
                                    ->minValue(1)
                                    ->label(__('global.mqty'))
                                    ->validationMessages([
                                        'required' => __('validation.required', ['attribute' => __('global.mqty')]),
                                    ]),
                                TextInput::make('name_show')
                                    ->required()
                                    ->maxLength(50)
                                    ->label(__('global.name_show'))
                                    ->validationMessages([
                                        'required' => __('validation.required', ['attribute' => __('global.name_show')]),
                                    ]),
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
                table: 'metrics',
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
