<?php

namespace App\Filament\Resources\Inventories\Pages;

use App\Filament\Resources\Inventories\InventoryResource;
use App\Filament\Resources\Inventories\Tables\InventoryMovementsTable;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table;

class ListInventoryMovements extends ListRecords
{
    protected static string $resource = InventoryResource::class;

    public function table(Table $table): Table
    {
        return InventoryMovementsTable::configure($table);
    }

    public function getTitle(): string
    {
        return __('global.movement_history');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('stock')
                ->label(__('global.inventory_stock'))
                ->icon('heroicon-o-archive-box')
                ->color('gray')
                ->url(fn () => InventoryResource::getUrl('index')),
        ];
    }
}
