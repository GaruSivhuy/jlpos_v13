<?php

namespace App\Filament\Resources\Inventories\Tables\Concerns;

use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

trait HasDateRangeFilter
{
    /**
     * @return array<DatePicker>
     */
    protected static function getDateRangeFilterFields(): array
    {
        return [
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
        ];
    }

    protected static function applyDateRangeFilter(Builder $query, array $data, string $column = 'created_at'): Builder
    {
        if (! empty($data['from_date']) && ! empty($data['to_date'])) {
            $fdate = Carbon::parse($data['from_date'])->format('Y-m-d');
            $tdate = Carbon::parse($data['to_date'])->format('Y-m-d');

            $query->where($column, '>=', $fdate.' 00:00:00');
            $query->where($column, '<=', $tdate.' 23:59:59');
        }

        return $query;
    }
}
