<?php

namespace App\Filament\Resources\Control\ChangeProducts;

use App\Filament\Resources\Control\ChangeProducts\Pages\CreateChangeProduct;
use App\Filament\Resources\Control\ChangeProducts\Pages\EditChangeProduct;
use App\Filament\Resources\Control\ChangeProducts\Pages\ListChangeProducts;
use App\Filament\Resources\Control\ChangeProducts\Schemas\ChangeProductForm;
use App\Filament\Resources\Control\ChangeProducts\Tables\ChangeProductsTable;
use App\Models\ChangeProduct;
use BackedEnum;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ChangeProductResource extends Resource implements HasShieldPermissions
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

    protected static ?string $model = ChangeProduct::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowPath;

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?string $slug = 'change-products';

    protected static ?int $navigationSort = 14;

    public static function form(Schema $schema): Schema
    {
        return ChangeProductForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ChangeProductsTable::configure($table);
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
            'index' => ListChangeProducts::route('/'),
            'create' => CreateChangeProduct::route('/create'),
            'edit' => EditChangeProduct::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['branch', 'inventories', 'metrics', 'user_create', 'user_updated'])
            ->branch();
    }

    /**
     * Once the stock was submitted the record is locked.
     */
    public static function canEdit(Model $record): bool
    {
        return (int) $record->submit_status === 0;
    }

    public static function getNavigationLabel(): string
    {
        return __('global.change_product_list');
    }

    public static function getModelLabel(): string
    {
        return __('global.change_product');
    }
}
