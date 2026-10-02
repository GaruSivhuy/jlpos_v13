<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\HasDateRangeFilter;
use App\Models\Invoice;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Carbon\CarbonInterface;
use Carbon\CarbonPeriod;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\DB;

/**
 * Paid / cancelled invoice amounts grouped by month of payment date,
 * filterable by a from_date - to_date range.
 */
class MonthlyInvoiceChart extends ChartWidget
{
    use HasDateRangeFilter;
    use HasWidgetShield;

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected ?string $maxHeight = '320px';

    public function getHeading(): string|Htmlable|null
    {
        return __('global.monthly_invoice_summary');
    }

    protected function getType(): string
    {
        return 'bar';
    }

    /**
     * @return array<string, mixed>
     */
    protected function getData(): array
    {
        return $this->rememberChartData(fn (): array => $this->buildData(), 300);
    }

    /**
     * @return array<string, mixed>
     */
    protected function buildData(): array
    {
        [$from, $to] = $this->getDateRange();

        $month = $this->monthExpression('payment_date');

        $totals = $this->whereInDateRange(Invoice::query(), 'payment_date')
            ->branch()
            ->whereIn('status', [Invoice::PAID, Invoice::CANCEL])
            ->selectRaw("{$month} as month")
            ->selectRaw('SUM(CASE WHEN status = ? THEN total ELSE 0 END) as paid_amount', [Invoice::PAID])
            ->selectRaw('SUM(CASE WHEN status = ? THEN total ELSE 0 END) as cancel_amount', [Invoice::CANCEL])
            ->groupByRaw($month)
            ->toBase()
            ->get()
            ->keyBy('month');

        // Every month in the range, so months without sales still show as 0.
        $months = collect(CarbonPeriod::create($from->copy()->startOfMonth(), '1 month', $to->copy()->startOfMonth()));

        return [
            'datasets' => [
                [
                    'label' => __('global.paid_invoices'),
                    'data' => $months->map(fn (CarbonInterface $date) => round((float) $totals->get($date->format('Y-m'))?->paid_amount, 2))->all(),
                    'backgroundColor' => '#00a65a',
                    'borderColor' => '#00a65a',
                ],
                [
                    'label' => __('global.cancelled_invoices'),
                    'data' => $months->map(fn (CarbonInterface $date) => round((float) $totals->get($date->format('Y-m'))?->cancel_amount, 2))->all(),
                    'backgroundColor' => '#dd4b39',
                    'borderColor' => '#dd4b39',
                ],
            ],
            'labels' => $months->map(fn (CarbonInterface $date) => $date->format('m/Y'))->all(),
        ];
    }

    protected function monthExpression(string $column): string
    {
        return match (DB::connection()->getDriverName()) {
            'sqlite' => "strftime('%Y-%m', {$column})",
            'pgsql' => "to_char({$column}, 'YYYY-MM')",
            default => "DATE_FORMAT({$column}, '%Y-%m')",
        };
    }
}
