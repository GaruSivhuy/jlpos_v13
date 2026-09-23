<?php

namespace App\Filament\Resources\Control\PaymentGateways\Pages;

use App\Filament\Resources\Control\PaymentGateways\PaymentGatewayResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;

class ListPaymentGateways extends ListRecords
{
    protected static string $resource = PaymentGatewayResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('global.create').' '.__('global.payment_gateway')),
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return __('global.payment_gateway');
    }
}
