<?php

namespace App\Filament\Resources\Inventories\Pages;

use App\Filament\Resources\Inventories\InventoryResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListInventories extends ListRecords
{
    protected static string $resource = InventoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('movements')
                ->label(__('global.movement_history'))
                ->icon('heroicon-o-clock')
                ->color('gray')
                ->url(fn () => InventoryResource::getUrl('movements')),
        ];
    }
}
