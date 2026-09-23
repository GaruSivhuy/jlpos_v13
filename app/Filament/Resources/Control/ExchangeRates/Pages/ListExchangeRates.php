<?php

namespace App\Filament\Resources\Control\ExchangeRates\Pages;

use App\Filament\Resources\Control\ExchangeRates\ExchangeRateResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;

class ListExchangeRates extends ListRecords
{
    protected static string $resource = ExchangeRateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('global.create').' '.__('global.exchange_rate')),
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return __('global.exchange_rate');
    }
}
