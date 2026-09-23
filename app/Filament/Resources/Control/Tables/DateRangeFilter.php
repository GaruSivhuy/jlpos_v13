<?php

namespace App\Filament\Resources\Control\Tables;

use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Grid;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class DateRangeFilter
{
    /**
     * Filters a date column between the "from" and "to" dates, both inclusive.
     */
    public static function make(string $column): Filter
    {
        return Filter::make('filter_date')
            ->columnSpan(3)
            ->schema([
                Grid::make(2)
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
                    ]),
            ])
            ->query(fn (Builder $query, array $data): Builder => $query
                ->when($data['from_date'] ?? null, fn (Builder $query, string $date): Builder => $query->whereDate($column, '>=', $date))
                ->when($data['to_date'] ?? null, fn (Builder $query, string $date): Builder => $query->whereDate($column, '<=', $date)));
    }
}
