<?php

namespace App\Filament\Resources\InventoryTransfers;

use App\Filament\Resources\InventoryTransfers\Pages\CreateInventoryTransfer;
use App\Filament\Resources\InventoryTransfers\Pages\EditInventoryTransfer;
use App\Filament\Resources\InventoryTransfers\Pages\ListInventoryTransfers;
use App\Filament\Resources\InventoryTransfers\Schemas\StockTransferForm;
use App\Filament\Resources\InventoryTransfers\Tables\StockTransfersTable;
use App\Models\StockTransfer;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class InventoryTransferResource extends Resource
{
    protected static ?string $model = StockTransfer::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowsRightLeft;

    protected static ?string $recordTitleAttribute = 'st_code';

    protected static ?string $slug = 'inventory-transfers';

    protected static ?int $navigationSort = 9;

    public static function getNavigationLabel(): string
    {
        return __('global.inventory_transfer');
    }

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('global.inventory_stock');
    }

    public static function getModelLabel(): string
    {
        return __('global.inventory_transfer');
    }

    public static function form(Schema $schema): Schema
    {
        return StockTransferForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StockTransfersTable::configure($table);
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
            'index' => ListInventoryTransfers::route('/'),
            'create' => CreateInventoryTransfer::route('/create'),
            'edit' => EditInventoryTransfer::route('/{record}/edit'),
        ];
    }
}
