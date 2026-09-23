<?php

namespace App\Filament\Resources\Control\OverMoney;

use App\Filament\Resources\Control\OverMoney\Pages\CreateOverMoney;
use App\Filament\Resources\Control\OverMoney\Pages\EditOverMoney;
use App\Filament\Resources\Control\OverMoney\Pages\ListOverMoney;
use App\Filament\Resources\Control\OverMoney\Schemas\OverMoneyForm;
use App\Filament\Resources\Control\OverMoney\Tables\OverMoneyTable;
use App\Models\OverMoney;
use BackedEnum;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class OverMoneyResource extends Resource implements HasShieldPermissions
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

    protected static ?string $model = OverMoney::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCurrencyDollar;

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?string $slug = 'over-money';

    protected static ?int $navigationSort = 15;

    public static function form(Schema $schema): Schema
    {
        return OverMoneyForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OverMoneyTable::configure($table);
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
            'index' => ListOverMoney::route('/'),
            'create' => CreateOverMoney::route('/create'),
            'edit' => EditOverMoney::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['branch', 'user_create', 'user_updated'])
            ->branch();
    }

    /**
     * An over money entry can only be corrected on the day it was made.
     */
    public static function canEdit(Model $record): bool
    {
        return $record->over_money_date === null || $record->over_money_date->greaterThanOrEqualTo(today());
    }

    public static function getNavigationLabel(): string
    {
        return __('global.over_money_list');
    }

    public static function getModelLabel(): string
    {
        return __('global.over_money');
    }
}
