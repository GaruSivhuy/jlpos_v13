<?php

namespace App\Filament\Resources\Control\ExchangeRates\Tables;

use App\Filament\Resources\Control\ExchangeRates\Schemas\ExchangeRateForm;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ExchangeRatesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label(__('global.id'))
                    ->sortable()
                    ->state(fn ($record) => 'EXC-'.str_pad($record->id, env('ID_PAD_LENGTH', 4), '0', STR_PAD_LEFT)),
                TextColumn::make('exchange_rate')->label(__('global.exchange_rate'))->numeric()->sortable(),
                TextColumn::make('exchange_rate_currency')
                    ->label(__('global.currency'))
                    ->formatStateUsing(fn ($state) => ExchangeRateForm::currencyOptions()[$state] ?? null),
                TextColumn::make('branch.name_kh')->label(__('global.branch')),
                TextColumn::make('user_create.name')->label(__('global.created_by')),
                TextColumn::make('created_at')->label(__('global.created_at'))->dateTime('d-m-Y H:i:s')->sortable(),
                TextColumn::make('updated_at')->label(__('global.updated_at'))->dateTime('d-m-Y H:i:s')->sortable(),
            ]);
    }
}
