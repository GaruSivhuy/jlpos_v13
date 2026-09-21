<?php

namespace App\Filament\Resources\InventoryTransfers\Pages;

use App\Filament\Resources\BaseCreateRecord;
use App\Filament\Resources\InventoryTransfers\InventoryTransferResource;

class CreateInventoryTransfer extends BaseCreateRecord
{
    protected static string $resource = InventoryTransferResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['transfer_date'] = now();
        $data['user_id'] = auth()->id();

        return $data;
    }
}
