<?php

namespace App\Filament\Resources\Remarks;

use App\Filament\Resources\Remarks\Pages\CreateRemark;
use App\Filament\Resources\Remarks\Pages\EditRemark;
use App\Filament\Resources\Remarks\Pages\ListRemarks;
use App\Filament\Resources\Remarks\Schemas\RemarkForm;
use App\Filament\Resources\Remarks\Tables\RemarksTable;
use App\Models\Remark;
use BackedEnum;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class RemarkResource extends Resource implements HasShieldPermissions
{
    public static function getPermissionPrefixes(): array
    {
        return [
            "view_any",
            "view",
            "create",
            "update",
            "delete",
        ];
    }

    protected static ?string $model = Remark::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ArchiveBoxXMark;

    protected static ?string $recordTitleAttribute = 'Remark';

    protected static ?string $slug = 'remarks';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return RemarkForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RemarksTable::configure($table);
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
            'index' => ListRemarks::route('/'),
            'create' => CreateRemark::route('/create'),
            'edit' => EditRemark::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getNavigationLabel(): string
    {
        return __("global.remarkable_list");
    }


    public static function getNavigationGroup(): string | UnitEnum | null
    {
        return __("global.product");
    }

    public static function getModelLabel(): string
    {
        return __('global.remarkable');
    }


    // public static function getEloquentQuery(): Builder
    // {
    //     // dd(parent::getEloquentQuery()->selectRaw('inventories.*')->with('category', 'metrics', 'remarks')->with('user_create')->with('user_update')->with('branch')->branch()->where('id', 19)->get());
    //     // return parent::getEloquentQuery()->selectRaw('inventories.*')->with('category', 'metrics', 'remarks')->with('user_create')->with('user_update')->with('branch')->branch();
    // }
}
