<?php

namespace App\Http\Controllers;

use App\Models\ChangeProduct;
use App\Models\ExchangeMoney;
use App\Models\Invoice;
use App\Models\MainCategory;
use App\Models\OverMoney;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Renders the non-Excel reports as a print-ready page, opened in a new tab from the Reports page.
 */
class ReportsPrintController extends Controller
{
    /**
     * @var array<int, string>
     */
    protected const array REPORT_TYPES = [
        'rpt_sale_total',
        'rpt_sale_total_detail',
        'rpt_sale_by_payment_gateway',
        'rpt_sale_by_payment_gateway_detail',
        'rpt_sale_by_main_cat_detail',
        'rpt_exchange_money',
        'rpt_over_money',
        'rpt_exchange_product',
    ];

    public function __invoke(Request $request): View
    {
        $data = $request->validate([
            'report_type' => ['required', Rule::in(self::REPORT_TYPES)],
            'from_date' => ['required', 'date'],
            'to_date' => ['required', 'date'],
            'main_cat_id' => ['nullable', 'integer'],
            'user_id' => ['nullable', 'integer'],
            'branch_id' => ['nullable', 'integer'],
        ]);

        $fromDate = $data['from_date'];
        $toDate = $data['to_date'];
        $userId = $data['user_id'] ?? null;
        $mainCatId = $data['main_cat_id'] ?? null;
        $branchId = $data['branch_id'] ?? null;
        $cashier = $userId ? User::find($userId)?->name : null;

        return match ($data['report_type']) {
            'rpt_sale_total', 'rpt_sale_total_detail' => view('reports.sale-total', [
                'results' => $this->saleTotalQuery($fromDate, $toDate, $userId, $branchId)->get(),
                'resultsGroupBy' => $this->saleTotalQuery($fromDate, $toDate, $userId, $branchId)->select('payment_date')->groupBy('payment_date')->get(),
                'fromDate' => $fromDate,
                'toDate' => $toDate,
                'cashier' => $cashier,
                'detail' => $data['report_type'] === 'rpt_sale_total_detail',
            ]),
            'rpt_sale_by_payment_gateway', 'rpt_sale_by_payment_gateway_detail' => view('reports.sale-by-payment', [
                'results' => $this->saleTotalQuery($fromDate, $toDate, $userId, $branchId)->with('paymentGateway')->get(),
                'resultsGroupBy' => $this->saleTotalQuery($fromDate, $toDate, $userId, $branchId)->select('payment_date')->groupBy('payment_date')->get(),
                'resultsGateway' => $this->saleTotalQuery($fromDate, $toDate, $userId, $branchId)->with('paymentGateway')->select('payment_gateway', 'payment_date')->groupBy('payment_gateway', 'payment_date')->get(),
                'fromDate' => $fromDate,
                'toDate' => $toDate,
                'cashier' => $cashier,
                'detail' => $data['report_type'] === 'rpt_sale_by_payment_gateway_detail',
            ]),
            'rpt_sale_by_main_cat_detail' => view('reports.sale-by-main-cat', [
                'results' => $this->saleByMainCatQuery($fromDate, $toDate, $mainCatId, $userId, $branchId)->get(),
                'resultsGroupBy' => $this->saleByMainCatQuery($fromDate, $toDate, $mainCatId, $userId, $branchId)->select('invoices.payment_date')->groupBy('invoices.payment_date')->get(),
                'resultsGateway' => $this->saleByMainCatQuery($fromDate, $toDate, $mainCatId, $userId, $branchId)->with('paymentGateway')->select('invoices.payment_gateway', 'invoices.payment_date')->groupBy('invoices.payment_gateway', 'invoices.payment_date')->get(),
                'mainCategory' => $mainCatId ? MainCategory::find($mainCatId) : null,
                'fromDate' => $fromDate,
                'toDate' => $toDate,
                'cashier' => $cashier,
            ]),
            'rpt_exchange_money' => view('reports.exchange-money', [
                'results' => $this->exchangeMoneyQuery($fromDate, $toDate, $userId, $branchId)->get(),
                'resultsGroupBy' => $this->exchangeMoneyQuery($fromDate, $toDate, $userId, $branchId)->select('exchange_date')->groupBy('exchange_date')->get(),
                'resultsType' => $this->exchangeMoneyQuery($fromDate, $toDate, $userId, $branchId)->select('exchange_type', 'exchange_date')->groupBy('exchange_type', 'exchange_date')->get(),
                'fromDate' => $fromDate,
                'toDate' => $toDate,
                'cashier' => $cashier,
            ]),
            'rpt_over_money' => view('reports.over-money', [
                'results' => $this->overMoneyQuery($fromDate, $toDate, $userId, $branchId)->get(),
                'resultsGroupBy' => $this->overMoneyQuery($fromDate, $toDate, $userId, $branchId)->select('over_money_date')->groupBy('over_money_date')->get(),
                'resultsType' => $this->overMoneyQuery($fromDate, $toDate, $userId, $branchId)->select('over_money_type', 'over_money_date')->groupBy('over_money_type', 'over_money_date')->get(),
                'fromDate' => $fromDate,
                'toDate' => $toDate,
                'cashier' => $cashier,
            ]),
            'rpt_exchange_product' => view('reports.change-product', [
                'results' => $this->changeProductQuery($fromDate, $toDate, $userId, $branchId)->get(),
                'resultsGroupBy' => $this->changeProductQuery($fromDate, $toDate, $userId, $branchId)->select('change_product_date')->groupBy('change_product_date')->get(),
                'resultsType' => $this->changeProductQuery($fromDate, $toDate, $userId, $branchId)->select('change_product_type', 'change_product_date')->groupBy('change_product_type', 'change_product_date')->get(),
                'fromDate' => $fromDate,
                'toDate' => $toDate,
                'cashier' => $cashier,
            ]),
        };
    }

    protected function saleTotalQuery(string $fromDate, string $toDate, mixed $userId, mixed $branchId): Builder
    {
        return Invoice::query()
            ->with('invoiceItems.product')
            ->branch()
            ->whereDate('payment_date', '>=', $fromDate)
            ->whereDate('payment_date', '<=', $toDate)
            ->when($userId, fn (Builder $query) => $query->where('user_id', $userId))
            ->when($branchId, fn (Builder $query) => $query->where('branch_id', $branchId))
            ->whereIn('status', [Invoice::PAID, Invoice::CANCEL]);
    }

    protected function saleByMainCatQuery(string $fromDate, string $toDate, mixed $mainCatId, mixed $userId, mixed $branchId): Builder
    {
        return Invoice::query()
            ->join('invoice_items', 'invoices.id', '=', 'invoice_items.invoice_id')
            ->leftJoin('inventories', 'invoice_items.inventory_id', '=', 'inventories.id')
            ->whereIn('invoices.status', [Invoice::PAID, Invoice::CANCEL])
            ->branch()
            ->whereDate('invoices.payment_date', '>=', $fromDate)
            ->whereDate('invoices.payment_date', '<=', $toDate)
            ->when($mainCatId, fn (Builder $query) => $query->where('inventories.main_cat_id', $mainCatId))
            ->when($userId, fn (Builder $query) => $query->where('invoices.user_id', $userId))
            ->when($branchId, fn (Builder $query) => $query->where('invoices.branch_id', $branchId))
            ->select('inventories.name_kh', 'invoice_items.price', 'invoice_items.qty', 'invoice_items.discount', 'invoice_items.discount_type', 'invoice_items.amount', 'invoice_items.invoice_id', 'invoice_items.metric_id', 'invoices.payment_gateway', 'invoices.payment_date', 'invoices.status');
    }

    protected function exchangeMoneyQuery(string $fromDate, string $toDate, mixed $userId, mixed $branchId): Builder
    {
        return ExchangeMoney::query()
            ->with(['user_create', 'exchange'])
            ->branch()
            ->whereDate('exchange_date', '>=', $fromDate)
            ->whereDate('exchange_date', '<=', $toDate)
            ->when($userId, fn (Builder $query) => $query->where('user_id', $userId))
            ->when($branchId, fn (Builder $query) => $query->where('branch_id', $branchId));
    }

    protected function overMoneyQuery(string $fromDate, string $toDate, mixed $userId, mixed $branchId): Builder
    {
        return OverMoney::query()
            ->with('user_create')
            ->branch()
            ->whereDate('over_money_date', '>=', $fromDate)
            ->whereDate('over_money_date', '<=', $toDate)
            ->when($userId, fn (Builder $query) => $query->where('user_id', $userId))
            ->when($branchId, fn (Builder $query) => $query->where('branch_id', $branchId));
    }

    protected function changeProductQuery(string $fromDate, string $toDate, mixed $userId, mixed $branchId): Builder
    {
        return ChangeProduct::query()
            ->with(['user_create', 'inventories', 'metrics'])
            ->branch()
            ->whereDate('change_product_date', '>=', $fromDate)
            ->whereDate('change_product_date', '<=', $toDate)
            ->when($userId, fn (Builder $query) => $query->where('user_id', $userId))
            ->when($branchId, fn (Builder $query) => $query->where('branch_id', $branchId));
    }
}
