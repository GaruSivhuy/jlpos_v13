<?php

namespace App\Filament\Resources\InventoryTransfers\Pages;

use App\Filament\Resources\BaseEditRecord;
use App\Filament\Resources\InventoryTransfers\InventoryTransferResource;
use Filament\Actions\DeleteAction;

class EditInventoryTransfer extends BaseEditRecord
{
    protected static string $resource = InventoryTransferResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
