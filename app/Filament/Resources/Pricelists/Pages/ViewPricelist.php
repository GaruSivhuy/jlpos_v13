<?php

namespace App\Filament\Resources\Pricelists\Pages;

use App\Filament\Resources\Pricelists\PricelistResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPricelist extends ViewRecord
{
    protected static string $resource = PricelistResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
