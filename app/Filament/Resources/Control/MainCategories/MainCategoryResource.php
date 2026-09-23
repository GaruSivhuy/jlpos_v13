<?php

namespace App\Filament\Resources\Control\MainCategories;

use App\Filament\Resources\Control\MainCategories\Pages\CreateMainCategory;
use App\Filament\Resources\Control\MainCategories\Pages\EditMainCategory;
use App\Filament\Resources\Control\MainCategories\Pages\ListMainCategories;
use App\Filament\Resources\Control\MainCategories\Schemas\MainCategoryForm;
use App\Filament\Resources\Control\MainCategories\Tables\MainCategoriesTable;
use App\Models\MainCategory;
use BackedEnum;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class MainCategoryResource extends Resource implements HasShieldPermissions
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

    protected static ?string $model = MainCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquares2x2;

    protected static ?string $recordTitleAttribute = 'cat_name_kh';

    protected static ?string $slug = 'main-categories';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return MainCategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MainCategoriesTable::configure($table);
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
            'index' => ListMainCategories::route('/'),
            'create' => CreateMainCategory::route('/create'),
            'edit' => EditMainCategory::route('/{record}/edit'),
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
        return __('global.main_category_list');
    }

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('global.control');
    }

    public static function getModelLabel(): string
    {
        return __('global.main_category');
    }
}
