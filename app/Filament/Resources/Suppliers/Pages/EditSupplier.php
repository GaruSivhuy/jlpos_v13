<?php

namespace App\Filament\Resources\Suppliers\Pages;

use App\Filament\Resources\BaseEditRecord;
use App\Filament\Resources\Suppliers\SupplierResource;
use Filament\Actions\DeleteAction;

class EditSupplier extends BaseEditRecord
{
    protected static string $resource = SupplierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
