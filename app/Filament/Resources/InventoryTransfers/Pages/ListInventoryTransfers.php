<?php

namespace App\Filament\Resources\InventoryTransfers\Pages;

use App\Filament\Resources\InventoryTransfers\InventoryTransferResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListInventoryTransfers extends ListRecords
{
    protected static string $resource = InventoryTransferResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
