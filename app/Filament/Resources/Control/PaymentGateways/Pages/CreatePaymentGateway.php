<?php

namespace App\Filament\Resources\Control\PaymentGateways\Pages;

use App\Filament\Resources\Control\ControlCreateRecord;
use App\Filament\Resources\Control\PaymentGateways\PaymentGatewayResource;
use Illuminate\Contracts\Support\Htmlable;

class CreatePaymentGateway extends ControlCreateRecord
{
    protected static string $resource = PaymentGatewayResource::class;

    protected static ?string $updatedByColumn = null;

    public function getTitle(): string|Htmlable
    {
        return __('global.create').' '.__('global.payment_gateway');
    }
}
