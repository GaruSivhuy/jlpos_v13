<?php

namespace App\Filament\Widgets;

use App\Models\Invoice;
use Filament\Widgets\Widget;

/**
 * Today's sale summary, same as the legacy vspos dashboard:
 * all / paid / cancelled invoice amounts by payment date.
 */
class DailySaleOverview extends Widget
{
    protected string $view = 'filament.widgets.daily-sale-overview';

    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    /**
     * @return array<string, float>
     */
    protected function getViewData(): array
    {
        $totals = Invoice::query()
            ->branch()
            ->whereIn('status', [Invoice::PAID, Invoice::CANCEL])
            ->where('payment_date', today()->toDateString())
            ->selectRaw('SUM(total) as total_amount')
            ->selectRaw('SUM(CASE WHEN status = ? THEN total ELSE 0 END) as total_paid_amount', [Invoice::PAID])
            ->selectRaw('SUM(CASE WHEN status = ? THEN total ELSE 0 END) as total_cancel_amount', [Invoice::CANCEL])
            ->toBase()
            ->first();

        return [
            'totalAmount' => (float) $totals?->total_amount,
            'totalPaidAmount' => (float) $totals?->total_paid_amount,
            'totalCancelAmount' => (float) $totals?->total_cancel_amount,
        ];
    }
}
