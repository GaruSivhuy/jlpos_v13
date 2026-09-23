<?php

namespace App\Filament\Resources\Control\PaymentGateways;

use App\Filament\Resources\Control\PaymentGateways\Pages\CreatePaymentGateway;
use App\Filament\Resources\Control\PaymentGateways\Pages\ListPaymentGateways;
use App\Filament\Resources\Control\PaymentGateways\Schemas\PaymentGatewayForm;
use App\Filament\Resources\Control\PaymentGateways\Tables\PaymentGatewaysTable;
use App\Models\PaymentGateway;
use BackedEnum;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class PaymentGatewayResource extends Resource implements HasShieldPermissions
{
    public static function getPermissionPrefixes(): array
    {
        return [
            'view_any',
            'view',
            'create',
        ];
    }

    protected static ?string $model = PaymentGateway::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCreditCard;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $slug = 'payment-gateways';

    protected static ?int $navigationSort = 6;

    public static function form(Schema $schema): Schema
    {
        return PaymentGatewayForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PaymentGatewaysTable::configure($table);
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
            'index' => ListPaymentGateways::route('/'),
            'create' => CreatePaymentGateway::route('/create'),
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
        return __('global.payment_gateway');
    }

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('global.control');
    }

    public static function getModelLabel(): string
    {
        return __('global.payment_gateway');
    }
}
