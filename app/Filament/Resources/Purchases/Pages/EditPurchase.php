<?php

namespace App\Filament\Resources\Purchases\Pages;

use App\Filament\Resources\BaseEditRecord;
use App\Filament\Resources\Purchases\PurchaseResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;

class EditPurchase extends BaseEditRecord
{
    protected static string $resource = PurchaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
