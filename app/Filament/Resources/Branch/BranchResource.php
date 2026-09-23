<?php

namespace App\Filament\Resources\Branch;

use App\Filament\Resources\Branch\Pages\CreateBranch;
use App\Filament\Resources\Branch\Pages\EditBranch;
use App\Filament\Resources\Branch\Pages\ListBranches;
use App\Filament\Resources\Branch\Schemas\BranchForm;
use App\Filament\Resources\Branch\Tables\BranchesTable;
use App\Models\Branch;
use BackedEnum;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class BranchResource extends Resource implements HasShieldPermissions
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

    protected static ?string $model = Branch::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::BuildingOffice2;

    protected static ?string $recordTitleAttribute = 'name_en';

    protected static ?string $slug = 'branches';

    protected static ?int $navigationSort = 98;

    public static function form(Schema $schema): Schema
    {
        return BranchForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BranchesTable::configure($table);
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
            'index' => ListBranches::route('/'),
            'create' => CreateBranch::route('/create'),
            'edit' => EditBranch::route('/{record}/edit'),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return __('global.branch_list');
    }

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('global.setting');
    }

    public static function getModelLabel(): string
    {
        return __('global.branch');
    }
}
