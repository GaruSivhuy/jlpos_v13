<?php

namespace App\Filament\Widgets\Concerns;

use Carbon\CarbonInterface;
use Closure;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Schema;
use Filament\Widgets\ChartWidget\Concerns\HasFiltersSchema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

/**
 * from_date - to_date filter for dashboard chart widgets, applied with the Apply button.
 * Defaults to the last 12 months, override getDefaultFromDate() to change it.
 */
trait HasDateRangeFilter
{
    use HasFiltersSchema;

    protected bool $hasDeferredFilters = true;

    /**
     * No auto refresh: polling re-renders the widget and closes the open filter dropdown.
     */
    protected function getPollingInterval(): ?string
    {
        return null;
    }

    public function getDescription(): string|Htmlable|null
    {
        [$from, $to] = $this->getDateRange();

        return $from->format('d-m-Y').' - '.$to->format('d-m-Y');
    }

    public function filtersSchema(Schema $schema): Schema
    {
        return $schema->components([
            DatePicker::make('from_date')
                ->label(__('global.from_date'))
                ->native(false)
                ->displayFormat('d-m-Y')
                ->default(fn (): CarbonInterface => $this->getDefaultFromDate())
                ->maxDate(now()),
            DatePicker::make('to_date')
                ->label(__('global.to_date'))
                ->native(false)
                ->displayFormat('d-m-Y')
                ->default(now())
                ->maxDate(now()),
        ]);
    }

    protected function getDefaultFromDate(): CarbonInterface
    {
        return now()->subMonths(11)->startOfMonth();
    }

    /**
     * @return array{0: CarbonInterface, 1: CarbonInterface}
     */
    protected function getDateRange(): array
    {
        $from = filled($this->filters['from_date'] ?? null)
            ? Carbon::parse($this->filters['from_date'])
            : $this->getDefaultFromDate();

        $to = filled($this->filters['to_date'] ?? null)
            ? Carbon::parse($this->filters['to_date'])
            : now();

        return $from->greaterThan($to) ? [$to->startOfDay(), $from->endOfDay()] : [$from->startOfDay(), $to->endOfDay()];
    }

    /**
     * Filter a date column by the selected range. Plain comparisons (not whereDate)
     * so MySQL can use the index on the column.
     */
    protected function whereInDateRange(Builder $query, string $column): Builder
    {
        [$from, $to] = $this->getDateRange();

        return $query
            ->where($column, '>=', $from->toDateString())
            ->where($column, '<=', $to->toDateString());
    }

    /**
     * Cache chart data per widget, user branches and date range, so reopening
     * the dashboard does not re-run the heavy queries on large data.
     *
     * @param  Closure(): array<string, mixed>  $callback
     * @return array<string, mixed>
     */
    protected function rememberChartData(Closure $callback, int $seconds = 600): array
    {
        [$from, $to] = $this->getDateRange();

        $user = auth()->user();
        $branches = $user->is_admin == 1 ? 'all' : $user->branch->pluck('id')->sort()->implode(',');

        $key = implode(':', ['dashboard', class_basename(static::class), app()->getLocale(), $branches, $from->toDateString(), $to->toDateString()]);

        return Cache::remember($key, $seconds, $callback);
    }
}
