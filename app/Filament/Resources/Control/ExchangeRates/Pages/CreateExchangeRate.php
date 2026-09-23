<?php

namespace App\Filament\Resources\Control\ExchangeRates\Pages;

use App\Filament\Resources\Control\ControlCreateRecord;
use App\Filament\Resources\Control\ExchangeRates\ExchangeRateResource;
use Illuminate\Contracts\Support\Htmlable;

class CreateExchangeRate extends ControlCreateRecord
{
    protected static string $resource = ExchangeRateResource::class;

    protected static ?string $updatedByColumn = null;

    public function getTitle(): string|Htmlable
    {
        return __('global.create').' '.__('global.exchange_rate');
    }
}
