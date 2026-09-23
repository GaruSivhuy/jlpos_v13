<?php

namespace App\Filament\Resources\Branch\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BranchForm
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
                                TextInput::make('name_en')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true)
                                    ->label(__('global.name_en'))
                                    ->validationMessages([
                                        'required' => __('validation.required', ['attribute' => __('global.name_en')]),
                                        'unique' => __('validation.unique', ['attribute' => __('global.name_en')]),
                                    ]),
                                TextInput::make('name_kh')
                                    ->maxLength(255)
                                    ->label(__('global.name_kh')),
                                Toggle::make('is_active')
                                    ->default(true)
                                    ->label(__('global.active')),
                            ]),
                    ]),
            ]);
    }
}
