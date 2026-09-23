<?php

namespace App\Filament\Resources\Control\ExchangeMoney\Schemas;

use App\Filament\Resources\Control\Schemas\BranchSelect;
use App\Models\ExchangeMoney;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class ExchangeMoneyForm
{
    /**
     * @return array<int, string>
     */
    public static function typeOptions(): array
    {
        return [
            ExchangeMoney::USD_TO_KHR => __('global.exchange_usd_to_khr'),
            ExchangeMoney::KHR_TO_USD => __('global.exchange_khr_to_usd'),
        ];
    }

    /**
     * USD => KHR multiplies the dollars by the rate, KHR => USD divides the riels by it.
     */
    public static function totalAmount(mixed $amount, mixed $rate, mixed $type): ?float
    {
        if (! is_numeric($amount) || ! is_numeric($rate) || (float) $rate <= 0) {
            return null;
        }

        return match ((int) $type) {
            ExchangeMoney::USD_TO_KHR => round((float) $amount * (float) $rate),
            ExchangeMoney::KHR_TO_USD => round((float) $amount / (float) $rate, 2),
            default => null,
        };
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
                                BranchSelect::make(),
                                Select::make('exchange_type')
                                    ->label(__('global.exchange_type'))
                                    ->required()
                                    ->searchable()
                                    ->live()
                                    ->options(static::typeOptions())
                                    ->afterStateUpdated(function (Set $set): void {
                                        $set('amount_exchange', null);
                                        $set('rate_exchange', null);
                                        $set('total_amount', null);
                                    })
                                    ->validationMessages([
                                        'required' => __('validation.required', ['attribute' => __('global.exchange_type')]),
                                    ]),
                                TextInput::make('amount_exchange')
                                    ->label(__('global.amount'))
                                    ->required()
                                    ->numeric()
                                    ->rule('gt:0')
                                    ->live(onBlur: true)
                                    ->suffix(fn (Get $get): ?string => match ((int) $get('exchange_type')) {
                                        ExchangeMoney::USD_TO_KHR => '$',
                                        ExchangeMoney::KHR_TO_USD => '៛',
                                        default => null,
                                    })
                                    ->afterStateUpdated(fn (Get $get, Set $set) => static::recalculate($get, $set))
                                    ->validationMessages([
                                        'required' => __('validation.required', ['attribute' => __('global.amount')]),
                                    ]),
                                TextInput::make('rate_exchange')
                                    ->label(__('global.exchange_rate'))
                                    ->required()
                                    ->numeric()
                                    ->rule('gt:0')
                                    ->live(onBlur: true)
                                    ->suffix('៛')
                                    ->afterStateUpdated(fn (Get $get, Set $set) => static::recalculate($get, $set))
                                    ->validationMessages([
                                        'required' => __('validation.required', ['attribute' => __('global.exchange_rate')]),
                                    ]),
                                TextInput::make('total_amount')
                                    ->label(__('global.total_amount'))
                                    ->readOnly()
                                    ->dehydrated(false)
                                    ->suffix(fn (Get $get): ?string => match ((int) $get('exchange_type')) {
                                        ExchangeMoney::USD_TO_KHR => '៛',
                                        ExchangeMoney::KHR_TO_USD => '$',
                                        default => null,
                                    }),
                            ]),
                    ]),
            ]);
    }

    protected static function recalculate(Get $get, Set $set): void
    {
        $set('total_amount', static::totalAmount($get('amount_exchange'), $get('rate_exchange'), $get('exchange_type')));
    }
}
