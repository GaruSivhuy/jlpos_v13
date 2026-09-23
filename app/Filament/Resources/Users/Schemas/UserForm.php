<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class UserForm
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
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255)
                                    ->label(__('global.user_name'))
                                    ->validationMessages([
                                        'required' => __('validation.required', ['attribute' => __('global.user_name')]),
                                    ]),
                                TextInput::make('email')
                                    ->email()
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true)
                                    ->label(__('global.email'))
                                    ->validationMessages([
                                        'required' => __('validation.required', ['attribute' => __('global.email')]),
                                        'unique' => __('validation.unique', ['attribute' => __('global.email')]),
                                    ]),
                                Select::make('roles')
                                    ->relationship('roles', 'name')
                                    ->multiple()
                                    ->preload()
                                    ->searchable()
                                    ->required()
                                    ->label(__('global.roles')),
                                Select::make('branch')
                                    ->relationship('branch', 'name_en', fn (Builder $query) => $query->where('is_active', 1))
                                    ->multiple()
                                    ->preload()
                                    ->searchable()
                                    ->required()
                                    ->label(__('global.branch')),
                                TextInput::make('password')
                                    ->password()
                                    ->revealable()
                                    ->required()
                                    ->confirmed()
                                    ->visibleOn('create')
                                    ->label(__('global.password'))
                                    ->validationMessages([
                                        'required' => __('validation.required', ['attribute' => __('global.password')]),
                                    ]),
                                TextInput::make('password_confirmation')
                                    ->password()
                                    ->revealable()
                                    ->required()
                                    ->dehydrated(false)
                                    ->visibleOn('create')
                                    ->label(__('global.confirm_password'))
                                    ->validationMessages([
                                        'required' => __('validation.required', ['attribute' => __('global.confirm_password')]),
                                    ]),
                                Toggle::make('active')
                                    ->default(true)
                                    ->label(__('global.active')),
                                Toggle::make('is_admin')
                                    ->default(false)
                                    ->label(__('global.is_admin')),
                            ]),
                    ]),
            ]);
    }
}
