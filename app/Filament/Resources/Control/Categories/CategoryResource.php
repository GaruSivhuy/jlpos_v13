<?php

namespace App\Filament\Resources\Control\Categories;

use App\Filament\Resources\Control\Categories\Pages\CreateCategory;
use App\Filament\Resources\Control\Categories\Pages\EditCategory;
use App\Filament\Resources\Control\Categories\Pages\ListCategories;
use App\Filament\Resources\Control\Categories\Schemas\CategoryForm;
use App\Filament\Resources\Control\Categories\Tables\CategoriesTable;
use App\Models\Category;
use BackedEnum;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class CategoryResource extends Resource implements HasShieldPermissions
{
    public static function getPermissionPrefixes(): array
    {
        return [
            'view_any',
            'view',
            'create',
            'update',
            'delete',
        ];
    }

    protected static ?string $model = Category::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static ?string $recordTitleAttribute = 'name_kh';

    protected static ?string $slug = 'categories';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return CategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CategoriesTable::configure($table);
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
            'index' => ListCategories::route('/'),
            'create' => CreateCategory::route('/create'),
            'edit' => EditCategory::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['branch', 'main_category', 'user_create', 'user_update'])
            ->branch();
    }

    public static function getNavigationLabel(): string
    {
        return __('global.category_list');
    }

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('global.control');
    }

    public static function getModelLabel(): string
    {
        return __('global.category');
    }
}
