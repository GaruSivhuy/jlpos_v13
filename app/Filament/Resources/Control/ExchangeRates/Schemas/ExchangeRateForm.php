<?php

namespace App\Filament\Resources\Control\ExchangeRates\Schemas;

use App\Filament\Resources\Control\Schemas\BranchSelect;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ExchangeRateForm
{
    /**
     * @return array<int, string>
     */
    public static function currencyOptions(): array
    {
        return [
            1 => 'KHR',
            2 => 'THB',
        ];
    }

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
                                TextInput::make('exchange_rate')
                                    ->required()
                                    ->numeric()
                                    ->rule('gt:0')
                                    ->label(__('global.exchange_rate'))
                                    ->validationMessages([
                                        'required' => __('validation.required', ['attribute' => __('global.exchange_rate')]),
                                    ]),
                                BranchSelect::make(),
                                Select::make('exchange_rate_currency')
                                    ->required()
                                    ->searchable()
                                    ->label(__('global.currency'))
                                    ->options(static::currencyOptions())
                                    ->validationMessages([
                                        'required' => __('validation.required', ['attribute' => __('global.currency')]),
                                    ]),
                            ]),
                    ]),
            ]);
    }
}
