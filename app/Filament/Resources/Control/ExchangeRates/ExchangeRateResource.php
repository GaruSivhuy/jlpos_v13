<?php

namespace App\Filament\Resources\Control\ExchangeRates;

use App\Filament\Resources\Control\ExchangeRates\Pages\CreateExchangeRate;
use App\Filament\Resources\Control\ExchangeRates\Pages\ListExchangeRates;
use App\Filament\Resources\Control\ExchangeRates\Schemas\ExchangeRateForm;
use App\Filament\Resources\Control\ExchangeRates\Tables\ExchangeRatesTable;
use App\Models\ExchangeRate;
use BackedEnum;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

/**
 * Every save adds a new row, the latest row of a branch is its current rate.
 */
class ExchangeRateResource extends Resource implements HasShieldPermissions
{
    public static function getPermissionPrefixes(): array
    {
        return [
            'view_any',
            'view',
            'create',
        ];
    }

    protected static ?string $model = ExchangeRate::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPresentationChartLine;

    protected static ?string $recordTitleAttribute = 'exchange_rate';

    protected static ?string $slug = 'exchange-rates';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return ExchangeRateForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ExchangeRatesTable::configure($table);
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
            'index' => ListExchangeRates::route('/'),
            'create' => CreateExchangeRate::route('/create'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['branch', 'user_create'])
            ->branch();
    }

    public static function getNavigationLabel(): string
    {
        return __('global.exchange_rate');
    }

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('global.control');
    }

    public static function getModelLabel(): string
    {
        return __('global.exchange_rate');
    }
}
