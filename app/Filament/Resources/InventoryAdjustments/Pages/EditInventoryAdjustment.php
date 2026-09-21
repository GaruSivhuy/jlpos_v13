<?php

namespace App\Filament\Resources\InventoryAdjustments\Pages;

use App\Filament\Resources\BaseEditRecord;
use App\Filament\Resources\InventoryAdjustments\InventoryAdjustmentResource;
use Filament\Actions\DeleteAction;

class EditInventoryAdjustment extends BaseEditRecord
{
    protected static string $resource = InventoryAdjustmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
