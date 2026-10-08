<?php

namespace App\Filament\Resources\Control\ServiceFees;

use App\Filament\Resources\Control\ServiceFees\Pages\CreateServiceFee;
use App\Filament\Resources\Control\ServiceFees\Pages\EditServiceFee;
use App\Filament\Resources\Control\ServiceFees\Pages\ListServiceFees;
use App\Filament\Resources\Control\ServiceFees\Schemas\ServiceFeeForm;
use App\Filament\Resources\Control\ServiceFees\Tables\ServiceFeeTable;
use App\Models\ServiceFee;
use BackedEnum;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ServiceFeeResource extends Resource implements HasShieldPermissions
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

    protected static ?string $model = ServiceFee::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?string $slug = 'service-fees';

    protected static ?int $navigationSort = 16;

    public static function form(Schema $schema): Schema
    {
        return ServiceFeeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ServiceFeeTable::configure($table);
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
            'index' => ListServiceFees::route('/'),
            'create' => CreateServiceFee::route('/create'),
            'edit' => EditServiceFee::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['user_create', 'user_updated']);
    }

    /**
     * A service fee can only be corrected on the day it was made.
     */
    public static function canEdit(Model $record): bool
    {
        return auth()->user()->is_admin || parent::canEdit($record) && ($record->created_at === null || $record->created_at->greaterThanOrEqualTo(today()));
    }

    public static function getNavigationLabel(): string
    {
        return __('global.service_fee_list');
    }

    public static function getModelLabel(): string
    {
        return __('global.service_fee');
    }
}
