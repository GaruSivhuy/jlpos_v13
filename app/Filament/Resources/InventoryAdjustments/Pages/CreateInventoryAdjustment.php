<?php

namespace App\Filament\Resources\InventoryAdjustments\Pages;

use App\Filament\Resources\BaseCreateRecord;
use App\Filament\Resources\InventoryAdjustments\InventoryAdjustmentResource;

class CreateInventoryAdjustment extends BaseCreateRecord
{
    protected static string $resource = InventoryAdjustmentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

        return $data;
    }
}
