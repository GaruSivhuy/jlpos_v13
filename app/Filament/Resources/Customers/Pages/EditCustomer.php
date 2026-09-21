<?php

namespace App\Filament\Resources\Customers\Pages;

use App\Filament\Resources\BaseEditRecord;
use App\Filament\Resources\Customers\CustomerResource;
use Filament\Actions\DeleteAction;
use Illuminate\Contracts\Support\Htmlable;

class EditCustomer extends BaseEditRecord
{
    protected static string $resource = CustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // DeleteAction::make(),
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return __('global.edit').' '.__('global.customer');
    }
}
