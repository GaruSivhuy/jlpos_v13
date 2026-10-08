<?php

namespace App\Filament\Resources\Control\ServiceFees\Pages;

use App\Filament\Resources\Control\ControlCreateRecord;
use App\Filament\Resources\Control\ServiceFees\ServiceFeeResource;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Js;

class CreateServiceFee extends ControlCreateRecord
{
    protected static string $resource = ServiceFeeResource::class;

    protected static ?string $updatedByColumn = 'user_update';

    public bool $printAfterCreate = false;

    protected function getFormActions(): array
    {
        return [
            $this->getCancelFormAction(),
            $this->getCreateAnotherFormAction(),
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

    /**
     * The receipt opens in a new tab to print, the page itself goes back to the list.
     */
    protected function afterCreate(): void
    {
        if ($this->printAfterCreate) {
            $this->js('window.open('.Js::from(route('service-fee.receipt', ['id' => $this->getRecord()->getKey()])).", '_blank')");
        }
    }

    public function getTitle(): string|Htmlable
    {
        return __('global.create').' '.__('global.service_fee');
    }
}
