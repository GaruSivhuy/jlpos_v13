<?php

namespace App\Filament\Resources\Control\ExchangeMoney\Pages;

use App\Filament\Resources\Control\ControlCreateRecord;
use App\Filament\Resources\Control\ExchangeMoney\ExchangeMoneyResource;
use App\Filament\Resources\Control\ExchangeMoney\Schemas\ExchangeMoneyForm;
use App\Models\ExchangeRate;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class CreateExchangeMoney extends ControlCreateRecord
{
    protected static string $resource = ExchangeMoneyResource::class;

    protected static ?string $updatedByColumn = 'user_update';

    public bool $printAfterCreate = false;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data = parent::mutateFormDataBeforeCreate($data);

        $data['total_amount'] = ExchangeMoneyForm::totalAmount($data['amount_exchange'], $data['rate_exchange'], $data['exchange_type']);
        $data['exchange_date'] = today();
        $data['exchange_id'] = ExchangeRate::query()
            ->where('branch_id', $data['branch_id'])
            ->latest('id')
            ->value('id');

        return $data;
    }

    protected function getFormActions(): array
    {
        return [
            $this->getCancelFormAction(),
            $this->getCreateAndPrintFormAction(),
            $this->getSubmitFormAction(),
        ];
    }

    protected function getCreateAndPrintFormAction(): Action
    {
        return Action::make('createAndPrint')
            ->label(__('global.print'))
            ->icon(Heroicon::OutlinedPrinter)
            ->color('info')
            ->action('createAndPrint');
    }

    public function createAndPrint(): void
    {
        $this->printAfterCreate = true;

        $this->create();
    }

    protected function getRedirectUrl(): string
    {
        if ($this->printAfterCreate) {
            return route('exchange-money.receipt', ['id' => $this->getRecord()->getKey()]);
        }

        return parent::getRedirectUrl();
    }

    public function getTitle(): string|Htmlable
    {
        return __('global.create').' '.__('global.exchange_money');
    }
}
