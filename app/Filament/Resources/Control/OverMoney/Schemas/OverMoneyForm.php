<?php

namespace App\Filament\Resources\Control\OverMoney\Schemas;

use App\Filament\Resources\Control\Schemas\BranchSelect;
use App\Models\OverMoney;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OverMoneyForm
{
    /**
     * @return array<int, string>
     */
    public static function currencyOptions(): array
    {
        return [
            OverMoney::USD => __('global.currency_usd'),
            OverMoney::KHR => __('global.currency_khr'),
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
                                TextInput::make('over_amount')
                                    ->label(__('global.amount'))
                                    ->required()
                                    ->numeric()
                                    ->rule('gt:0')
                                    ->validationMessages([
                                        'required' => __('validation.required', ['attribute' => __('global.amount')]),
                                    ]),
                                BranchSelect::make(),
                                Select::make('over_money_type')
                                    ->label(__('global.currency'))
                                    ->required()
                                    ->searchable()
                                    ->options(static::currencyOptions())
                                    ->validationMessages([
                                        'required' => __('validation.required', ['attribute' => __('global.currency')]),
                                    ]),
                            ]),
                    ]),
            ]);
    }
}
