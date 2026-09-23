<?php

namespace App\Filament\Resources\Control\Metrics;

use App\Filament\Resources\Control\Metrics\Pages\CreateMetric;
use App\Filament\Resources\Control\Metrics\Pages\EditMetric;
use App\Filament\Resources\Control\Metrics\Pages\ListMetrics;
use App\Filament\Resources\Control\Metrics\Schemas\MetricForm;
use App\Filament\Resources\Control\Metrics\Tables\MetricsTable;
use App\Models\Metric;
use BackedEnum;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class MetricResource extends Resource implements HasShieldPermissions
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

    protected static ?string $model = Metric::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedScale;

    protected static ?string $recordTitleAttribute = 'name_kh';

    protected static ?string $slug = 'metrics';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return MetricForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MetricsTable::configure($table);
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
            'index' => ListMetrics::route('/'),
            'create' => CreateMetric::route('/create'),
            'edit' => EditMetric::route('/{record}/edit'),
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
        return __('global.metric_list');
    }

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('global.control');
    }

    public static function getModelLabel(): string
    {
        return __('global.metric');
    }
}
