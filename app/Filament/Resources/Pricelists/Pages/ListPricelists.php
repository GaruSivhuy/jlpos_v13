<?php

namespace App\Filament\Resources\Pricelists\Pages;

use App\Filament\Resources\Pricelists\PricelistResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;

class ListPricelists extends ListRecords
{
    protected static string $resource = PricelistResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // CreateAction::make(),
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return __("global.price_list");
    }
}
