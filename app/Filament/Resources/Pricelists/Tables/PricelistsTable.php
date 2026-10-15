<?php

namespace App\Filament\Resources\Pricelists\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class PricelistsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('pbar_code')->label(__('global.pbar_code'))
                    ->state(function ($record) {
                        return $record->inventory?->pbar_code;
                    })->copyable(),
                TextColumn::make('inventory.name_kh')->label(__('global.product'))->searchable(),
                TextColumn::make('metric.name')->label(__('global.mname')),
                TextColumn::make('price')->label(__('global.price_whole')),
                TextColumn::make('user_updated.name')->label(__('global.updated_by')),
                TextColumn::make('inventory.branch.name_kh')->label(__('global.branch')),
                TextColumn::make('updated_at')->label(__('global.updated_at')),

            ])
            ->filters([
                // TrashedFilter::make(),
                static::getDataFilter(),
            ], layout: FiltersLayout::AboveContent)
            ->filtersFormColumns(1)
            ->defaultSort('id', 'desc')
            ->striped()
            ->persistFiltersInSession()
            ->searchOnBlur(true)
            ->recordActions([
                // ViewAction::make(),
                EditAction::make()
                    ->hiddenLabel()
                    ->tooltip('កំណត់តម្លៃ')
                    ->icon(Heroicon::CurrencyDollar),
            ], position: RecordActionsPosition::BeforeCells)
            ->toolbarActions([
                // BulkActionGroup::make([
                //     DeleteBulkAction::make(),
                //     ForceDeleteBulkAction::make(),
                //     RestoreBulkAction::make(),
                // ]),
            ]);
    }

    protected static function getDataFilter(): Filter
    {
        return Filter::make('filter_data')
            ->columnSpanFull()
            ->schema([
                Grid::make(4)
                    ->schema([
                        DatePicker::make('from_date')
                            ->label(__('global.from_date'))
                            ->native(false)
                            ->suffixIcon('heroicon-o-calendar')
                            ->displayFormat('d-m-Y')
                            ->placeholder('DD-MM-YYYY'),
                        DatePicker::make('to_date')
                            ->label(__('global.to_date'))
                            ->native(false)
                            ->suffixIcon('heroicon-o-calendar')
                            ->displayFormat('d-m-Y')
                            ->placeholder('DD-MM-YYYY'),
                        TextInput::make('pbar_code')
                            ->label(__('global.pbar_code')),
                        TextInput::make('name_kh')
                            ->label(__('global.product')),
                    ]),
            ])
            ->query(function (Builder $query, array $data): Builder {
                $table = $query->getModel()->getTable();

                return $query
                    ->when($data['from_date'] ?? null, fn (Builder $q, $date) => $q->where($table.'.created_at', '>=', Carbon::parse($date)->startOfDay()))
                    ->when($data['to_date'] ?? null, fn (Builder $q, $date) => $q->where($table.'.created_at', '<=', Carbon::parse($date)->endOfDay()))
                    ->when($data['pbar_code'] ?? null, fn (Builder $q, $code) => $q->whereHas('inventory', fn (Builder $iq) => $iq->where('pbar_code', 'like', '%'.$code.'%')))
                    ->when($data['name_kh'] ?? null, fn (Builder $q, $name) => $q->whereHas('inventory', fn (Builder $iq) => $iq->where('name_kh', 'like', '%'.$name.'%')));
            });
    }
}
