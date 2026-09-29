<x-filament-widgets::widget>
    <style>
        .daily-sale-title { font-size: 1rem; font-weight: 700; margin-bottom: 0.75rem; }
        .daily-sale-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1rem; }
        @media (min-width: 768px) { .daily-sale-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); } }
        .daily-sale-box { border-radius: 0.5rem; padding: 1rem 1.25rem; color: #fff; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.15); }
        .daily-sale-box h3 { font-size: 1.75rem; font-weight: 700; line-height: 1.2; margin: 0 0 0.5rem; white-space: nowrap; }
        .daily-sale-box p { text-align: right; font-size: 1rem; margin: 0; }
        .daily-sale-box.is-all { background-color: #00c0ef; }
        .daily-sale-box.is-paid { background-color: #00a65a; }
        .daily-sale-box.is-cancel { background-color: #dd4b39; }
    </style>

    <h2 class="daily-sale-title">{{ __('global.daily_sale_data') }}</h2>

    <div class="daily-sale-grid">
        <div class="daily-sale-box is-all">
            <h3>{{ number_format($totalAmount, 2) }} $</h3>
            <p>{{ __('global.all_invoices') }}</p>
        </div>

        <div class="daily-sale-box is-paid">
            <h3>{{ number_format($totalPaidAmount, 2) }} $</h3>
            <p>{{ __('global.paid_invoices') }}</p>
        </div>

        <div class="daily-sale-box is-cancel">
            <h3>{{ number_format($totalCancelAmount, 2) }} $</h3>
            <p>{{ __('global.cancelled_invoices') }}</p>
        </div>
    </div>
</x-filament-widgets::widget>
