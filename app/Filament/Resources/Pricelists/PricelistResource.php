<?php

namespace App\Filament\Resources\Pricelists;

use App\Filament\Resources\Pricelists\Pages\CreatePricelist;
use App\Filament\Resources\Pricelists\Pages\EditPricelist;
use App\Filament\Resources\Pricelists\Pages\ListPricelists;
use App\Filament\Resources\Pricelists\Pages\ViewPricelist;
use App\Filament\Resources\Pricelists\Schemas\PricelistForm;
use App\Filament\Resources\Pricelists\Schemas\PricelistInfolist;
use App\Filament\Resources\Pricelists\Tables\PricelistsTable;
use App\Models\Metricsables;
use BackedEnum;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class PricelistResource extends Resource implements HasShieldPermissions
{
    public static function getPermissionPrefixes(): array
    {
        return [
            "view_any",
            "view",
            "create",
            "update",
            "delete",
        ];
    }

    protected static ?string $model = Metricsables::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::CurrencyDollar;

    protected static ?string $recordTitleAttribute = 'Metricsables';

    protected static ?string $slug = 'price-list';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return PricelistForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PricelistInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PricelistsTable::configure($table);
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
            'index' => ListPricelists::route('/'),
            // 'create' => CreatePricelist::route('/create'),
            'view' => ViewPricelist::route('/{record}'),
            'edit' => EditPricelist::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getNavigationLabel(): string
    {
        return __("global.price_list");
    }


    public static function getNavigationGroup(): string | UnitEnum | null
    {
        return __("global.product");
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('inventory')->with('inventory.branch')->with('metric')->with('user_updated');
    }
}
