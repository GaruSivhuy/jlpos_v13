<?php

namespace App\Filament\Resources\Control\ExchangeMoney;

use App\Filament\Resources\Control\ExchangeMoney\Pages\CreateExchangeMoney;
use App\Filament\Resources\Control\ExchangeMoney\Pages\EditExchangeMoney;
use App\Filament\Resources\Control\ExchangeMoney\Pages\ListExchangeMoney;
use App\Filament\Resources\Control\ExchangeMoney\Schemas\ExchangeMoneyForm;
use App\Filament\Resources\Control\ExchangeMoney\Tables\ExchangeMoneyTable;
use App\Models\ExchangeMoney;
use BackedEnum;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ExchangeMoneyResource extends Resource implements HasShieldPermissions
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

    protected static ?string $model = ExchangeMoney::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCurrencyDollar;

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?string $slug = 'exchange-money';

    protected static ?int $navigationSort = 13;

    public static function form(Schema $schema): Schema
    {
        return ExchangeMoneyForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ExchangeMoneyTable::configure($table);
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
            'index' => ListExchangeMoney::route('/'),
            'create' => CreateExchangeMoney::route('/create'),
            'edit' => EditExchangeMoney::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['branch', 'user_create', 'user_updated'])
            ->branch();
    }

    /**
     * An exchange can only be corrected on the day it was made.
     */
    public static function canEdit(Model $record): bool
    {
        return $record->exchange_date === null || $record->exchange_date->greaterThanOrEqualTo(today());
    }

    public static function getNavigationLabel(): string
    {
        return __('global.exchange_money_list');
    }

    public static function getModelLabel(): string
    {
        return __('global.exchange_money');
    }
}
