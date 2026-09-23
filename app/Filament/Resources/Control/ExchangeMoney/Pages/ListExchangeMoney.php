<?php

namespace App\Filament\Resources\Control\ExchangeMoney\Pages;

use App\Filament\Resources\Control\ExchangeMoney\ExchangeMoneyResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;

class ListExchangeMoney extends ListRecords
{
    protected static string $resource = ExchangeMoneyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('global.create').' '.__('global.exchange_money')),
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return __('global.exchange_money_list');
    }
}
