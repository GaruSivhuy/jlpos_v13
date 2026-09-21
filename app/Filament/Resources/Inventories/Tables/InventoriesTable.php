<?php

namespace App\Filament\Resources\Inventories\Tables;

use App\Filament\Resources\Inventories\Tables\Concerns\HasDateRangeFilter;
use App\Models\Category;
use App\Models\MainCategory;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class InventoriesTable
{
    use HasDateRangeFilter;

    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('id')->label(__('global.id')),
                TextColumn::make('item.name_kh')->label(__('global.pname_kh'))->searchable(),
                TextColumn::make('quantity')->label(__('global.quantity'))->numeric(),
                TextColumn::make('location.name_kh')->label(__('global.location')),
                // TextColumn::make('metric.name_kh')->label(__('global.mname')),
                TextColumn::make('created_at')->label(__('global.created_at')),
                TextColumn::make('updated_at')->label(__('global.updated_at')),
            ])
            ->filters([
                static::getDataFilter(),
            ], layout: FiltersLayout::AboveContent)
            ->filtersFormWidth('4xl')
            ->filtersFormColumns(1)
            ->filtersFormMaxHeight('400px');
    }

    protected static function getDataFilter(): Filter
    {
        return Filter::make('filter_data')
            ->columnSpan(3)
            ->schema([
                Grid::make(4)
                    ->schema([
                        ...static::getDateRangeFilterFields(),
                        Select::make('main_cat_id')
                            ->label(__('global.main_category'))
                            ->options(MainCategory::select(DB::raw('CONCAT(cat_name_kh) AS categories_name'), 'id')->branch()->pluck('categories_name', 'id')->toArray())
                            ->searchable(),
                        Select::make('category_id')
                            ->label(__('global.cname_kh'))
                            ->options(function (Get $get) {
                                return $get('main_cat_id') ? Category::select(DB::raw('CONCAT(name_kh) AS name_kh'), 'id')->where('main_cat_id', $get('main_cat_id'))->branch()->pluck('name_kh', 'id')->toArray() : [];
                            })
                            ->searchable(),
                        TextInput::make('name_kh')
                            ->label(__('global.pname_kh')),
                        TextInput::make('pbar_code')
                            ->label(__('global.pbar_code')),
                    ]),
            ])
            ->query(function (Builder $query, array $data): Builder {
                $query = static::applyDateRangeFilter($query, $data);

                if (! empty($data['main_cat_id'])) {
                    $mainCatId = $data['main_cat_id'];
                    $query->whereHas('item', fn (Builder $item) => $item->where('main_cat_id', $mainCatId));
                }

                if (! empty($data['category_id'])) {
                    $categoryId = $data['category_id'];
                    $query->whereHas('item', fn (Builder $item) => $item->where('category_id', $categoryId));
                }

                if (! empty($data['name_kh'])) {
                    $nameKh = $data['name_kh'];
                    $query->whereHas('item', fn (Builder $item) => $item->where('name_kh', 'like', '%'.$nameKh.'%'));
                }

                if (! empty($data['pbar_code'])) {
                    $pbarCode = $data['pbar_code'];
                    $query->whereHas('item', fn (Builder $item) => $item->where('pbar_code', $pbarCode));
                }

                return $query;
            });
    }
}
