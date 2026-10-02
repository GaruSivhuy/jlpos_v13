<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\HasDateRangeFilter;
use App\Models\Invoice;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Carbon\CarbonInterface;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

/**
 * Top 10 best selling products by quantity on paid invoices,
 * same rule as the best sale Excel report (App\Exports\BestSaleReport).
 */
class TopSellingProductChart extends ChartWidget
{
    use HasDateRangeFilter;
    use HasWidgetShield;

    protected const LIMIT = 10;

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    protected ?string $maxHeight = '400px';

    public function getHeading(): string|Htmlable|null
    {
        return __('global.top_selling_products');
    }

    /**
     * Default from the first paid invoice, so the chart covers the whole system period.
     */
    protected function getDefaultFromDate(): CarbonInterface
    {
        return once(function (): CarbonInterface {
            $user = auth()->user();
            $branches = $user->is_admin == 1 ? 'all' : $user->branch->pluck('id')->sort()->implode(',');

            // The first sale date never changes, cache it for a day.
            $firstPaymentDate = Cache::remember("dashboard:first_payment_date:{$branches}", now()->addDay(), fn () => Invoice::query()
                ->branch()
                ->where('status', Invoice::PAID)
                ->min('payment_date'));

            return $firstPaymentDate ? Carbon::parse($firstPaymentDate) : now()->startOfYear();
        })->copy();
    }

    protected function getType(): string
    {
        return 'bar';
    }

    /**
     * @return array<string, mixed>
     */
    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y',
            'plugins' => [
                'legend' => ['display' => false],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getData(): array
    {
        return $this->rememberChartData(fn (): array => $this->buildData());
    }

    /**
     * @return array<string, mixed>
     */
    protected function buildData(): array
    {
        $products = $this->whereInDateRange(Invoice::query(), 'invoices.payment_date')
            ->branch()
            ->join('invoice_items', 'invoices.id', '=', 'invoice_items.invoice_id')
            ->join('inventories', 'invoice_items.inventory_id', '=', 'inventories.id')
            ->whereNull('invoice_items.deleted_at')
            ->where('invoices.status', Invoice::PAID)
            ->groupBy('inventories.id', 'inventories.name', 'inventories.name_kh')
            ->selectRaw('inventories.name, inventories.name_kh, SUM(invoice_items.quantity) as total_qty')
            ->orderByDesc('total_qty')
            ->limit(static::LIMIT)
            ->toBase()
            ->get();

        return [
            'datasets' => [
                [
                    'label' => __('global.quantity'),
                    'data' => $products->map(fn (object $product) => (float) $product->total_qty)->all(),
                    'backgroundColor' => '#00c0ef',
                    'borderColor' => '#00c0ef',
                ],
            ],
            'labels' => $products->map(fn (object $product) => $product->name_kh ?: $product->name)->all(),
        ];
    }
}
