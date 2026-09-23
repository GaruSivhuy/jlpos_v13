<?php

namespace App\Filament\Resources\Control\Locations;

use App\Filament\Resources\Control\Locations\Pages\CreateLocation;
use App\Filament\Resources\Control\Locations\Pages\EditLocation;
use App\Filament\Resources\Control\Locations\Pages\ListLocations;
use App\Filament\Resources\Control\Locations\Schemas\LocationForm;
use App\Filament\Resources\Control\Locations\Tables\LocationsTable;
use App\Models\Location;
use BackedEnum;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class LocationResource extends Resource implements HasShieldPermissions
{
    public static function getPermissionPrefixes(): array
    {
        return [
            'view_any',
            'view',
            'create',
            'update',
        ];
    }

    protected static ?string $model = Location::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMapPin;

    protected static ?string $recordTitleAttribute = 'name_kh';

    protected static ?string $slug = 'locations';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return LocationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LocationsTable::configure($table);
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
            'index' => ListLocations::route('/'),
            'create' => CreateLocation::route('/create'),
            'edit' => EditLocation::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['branch', 'user_create', 'user_update'])
            ->branch();
    }

    public static function getNavigationLabel(): string
    {
        return __('global.location_list');
    }

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('global.control');
    }

    public static function getModelLabel(): string
    {
        return __('global.location');
    }
}
