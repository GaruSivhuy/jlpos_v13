<?php

namespace App\Filament\Resources\Inventories\Tables;

use App\Filament\Resources\Inventories\Tables\Concerns\HasDateRangeFilter;
use App\Stevebauman\Inventory\Models\InventoryStockMovement;
use Filament\Schemas\Components\Grid;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class InventoryMovementsTable
{
    use HasDateRangeFilter;

    public static function configure(Table $table): Table
    {
        return $table
            ->query(InventoryStockMovement::query()->with(['stock.item', 'stock.location', 'stock.metric', 'user']))
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('id')->label(__('global.id')),
                TextColumn::make('stock.item.name_kh')->label(__('global.pname_kh'))->searchable(),
                TextColumn::make('stock.location.name_kh')->label(__('global.location')),
                // TextColumn::make('stock.metric.name_kh')->label(__('global.mname')),
                TextColumn::make('before')->label(__('global.stock_before')),
                TextColumn::make('after')->label(__('global.stock_after')),
                TextColumn::make('remain')
                    ->label(__('global.stock_quantity'))
                    ->state(fn ($record) => $record->after - $record->before),
                TextColumn::make('reason')->label(__('global.reason')),
                TextColumn::make('user.name')->label(__('global.updated_by')),
                TextColumn::make('created_at')->label(__('global.created_at')),
            ])
            ->filters([
                static::getDataFilter(),
            ], layout: FiltersLayout::AboveContent)
            ->filtersFormWidth('2xl')
            ->filtersFormColumns(1)
            ->filtersFormMaxHeight('400px');
    }

    protected static function getDataFilter(): Filter
    {
        return Filter::make('filter_data')
            ->columnSpan(3)
            ->schema([
                Grid::make(2)
                    ->schema(static::getDateRangeFilterFields()),
            ])
            ->query(fn (Builder $query, array $data): Builder => static::applyDateRangeFilter($query, $data));
    }
}
