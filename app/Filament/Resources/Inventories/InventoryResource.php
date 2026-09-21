<?php

namespace App\Filament\Resources\Inventories;

use App\Filament\Resources\Inventories\Pages\ListInventories;
use App\Filament\Resources\Inventories\Pages\ListInventoryMovements;
use App\Filament\Resources\Inventories\Tables\InventoriesTable;
use App\Stevebauman\Inventory\Models\InventoryStock;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class InventoryResource extends Resource
{
    protected static ?string $model = InventoryStock::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArchiveBox;

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?string $slug = 'inventory-stocks';

    protected static ?int $navigationSort = 7;

    public static function getNavigationLabel(): string
    {
        return __('global.inventory_stock');
    }

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('global.inventory_stock');
    }

    public static function getModelLabel(): string
    {
        return __('global.inventory_stock');
    }

    public static function table(Table $table): Table
    {
        return InventoriesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInventories::route('/'),
            'movements' => ListInventoryMovements::route('/movements'),
        ];
    }
}
