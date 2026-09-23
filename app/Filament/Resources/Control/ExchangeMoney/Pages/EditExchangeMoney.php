<?php

namespace App\Filament\Resources\Control\ExchangeMoney\Pages;

use App\Filament\Resources\Control\ControlEditRecord;
use App\Filament\Resources\Control\ExchangeMoney\ExchangeMoneyResource;
use App\Filament\Resources\Control\ExchangeMoney\Schemas\ExchangeMoneyForm;
use Illuminate\Contracts\Support\Htmlable;

class EditExchangeMoney extends ControlEditRecord
{
    protected static string $resource = ExchangeMoneyResource::class;

    protected static string $updatedByColumn = 'user_update';

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data = parent::mutateFormDataBeforeSave($data);
        $data['total_amount'] = ExchangeMoneyForm::totalAmount($data['amount_exchange'], $data['rate_exchange'], $data['exchange_type']);

        return $data;
    }

    public function getTitle(): string|Htmlable
    {
        return __('global.edit').' '.__('global.exchange_money');
    }
}
