<?php

namespace App\Filament\Resources\Sale\Pages;

use App\Filament\Resources\Sale\InvoiceResource;
use App\Models\Category;
use App\Models\Customer;
use App\Models\ExchangeRate;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Location;
use App\Models\PaymentGateway;
use App\Models\Product;
use App\Stevebauman\Inventory\Exceptions\NotEnoughStockException;
use App\Stevebauman\Inventory\Models\InventoryStock;
use Closure;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use RuntimeException;
use Vinkla\Hashids\Facades\Hashids;

class Pos extends Page
{
    protected static string $resource = InvoiceResource::class;

    protected string $view = 'filament.resources.sale.pages.pos';

    /**
     * Hashed id of the invoice being edited, empty for a new sale.
     */
    #[Url(as: 'sale_id')]
    public ?string $saleId = null;

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    public int|string|null $locationId = null;

    public int|string|null $categoryId = null;

    public string $search = '';

    /**
     * Selected metric per inventory stock id.
     *
     * @var array<int, int|string|null>
     */
    public array $metricId = [];

    /**
     * Quantity to add per inventory stock id.
     *
     * @var array<int, int|string|null>
     */
    public array $qty = [];

    public function getTitle(): string
    {
        return __('global.pos');
    }

    public function mount(): void
    {
        $invoice = null;

        if (filled($this->saleId)) {
            $invoice = $this->invoice;

            abort_if($invoice === null, 404);
        }

        $this->form->fill([
            'customer_id' => $invoice?->customer_id,
            'invoiced_at' => ($invoice->invoiced_at ?? now())->format('d/m/Y'),
            'discount' => $invoice->discount ?? 0,
            'sale_by' => $invoice->createdBy->name ?? auth()->user()->name,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Select::make('customer_id')
                    ->label(__('global.customer'))
                    ->placeholder(__('global.select_customer'))
                    ->searchable()
                    ->preload()
                    ->options(fn (): array => $this->customerOptions())
                    ->getSearchResultsUsing(fn (string $search): array => $this->customerOptions($search))
                    ->getOptionLabelUsing(fn ($value): ?string => ($customer = Customer::find($value)) ? $this->customerLabel($customer) : null)
                    ->disabled(fn () => $this->isLocked())
                    ->live()
                    ->afterStateUpdated(fn ($state) => $this->saveCustomer($state)),
                TextInput::make('invoiced_at')
                    ->label(__('global.invoiced_at'))
                    ->readOnly(),
                TextInput::make('discount')
                    ->label(__('global.discount').' ('.env('FOREIGN_CURRENCY_SIGN', '$').')')
                    ->numeric()
                    ->minValue(0)
                    ->disabled(fn () => $this->invoice === null || $this->isLocked())
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn () => $this->applyInvoiceDiscount()),
                TextInput::make('sale_by')
                    ->label(__('global.sale_by'))
                    ->readOnly(),
            ])
            ->statePath('data');
    }

    /**
     * Searchable category filter, bound straight to the $categoryId property.
     */
    public function categoryFilterForm(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('categoryId')
                ->hiddenLabel()
                ->placeholder(__('global.cname_kh').' : '.__('global.sale_all'))
                ->options(fn (): array => $this->categories)
                ->searchable()
                ->live(),
        ]);
    }

    /**
     * Searchable location filter, bound straight to the $locationId property.
     */
    public function locationFilterForm(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('locationId')
                ->hiddenLabel()
                ->placeholder(__('global.location').' : '.__('global.sale_all'))
                ->options(fn (): array => $this->locations)
                ->searchable()
                ->live(),
        ]);
    }

    /**
     * One searchable metric select per listed product, bound to $metricId[inventory stock id].
     *
     * Every listed stock gets a null entry first: the select only shows its placeholder for null or
     * an empty string, and would otherwise treat the missing (undefined) entry as a selected value.
     * Every listed stock also starts with a quantity of 1.
     */
    public function metricSelectForm(Schema $schema): Schema
    {
        return $schema->components(fn (): array => $this->products
            ->each(function (Product $product): void {
                $this->metricId[$product->inventory_stock_id] ??= null;
                $this->qty[$product->inventory_stock_id] ??= 1;
            })
            ->map(fn (Product $product): Select => Select::make('metricId.'.$product->inventory_stock_id)
                ->hiddenLabel()
                ->placeholder(__('global.sale_select_metric'))
                ->options($product->metrics->pluck('name_kh', 'id')->all())
                ->searchable()
                ->live())
            ->all());
    }

    #[Computed]
    public function invoice(): ?Invoice
    {
        $id = filled($this->saleId) ? (Hashids::connection(Invoice::class)->decode($this->saleId)[0] ?? null) : null;

        if ($id === null) {
            return null;
        }

        return Invoice::query()
            ->branch()
            ->with(['invoiceItems.product', 'invoiceItems.metric', 'invoicePayments', 'exchange', 'createdBy'])
            ->find($id);
    }

    /**
     * @return array<string, float>
     */
    #[Computed]
    public function totals(): array
    {
        $invoice = $this->invoice;

        $subTotal = (float) $invoice?->invoiceItems->sum('amount');
        $discount = (float) $invoice?->discount;
        $total = $subTotal - $discount;
        $exchangeRate = (float) $invoice?->exchange?->exchange_rate;

        return [
            'sub_total' => $subTotal,
            'discount' => $discount,
            'total' => $total,
            'exchange_rate' => $exchangeRate,
            'total_in_riel' => $total * $exchangeRate,
            'paid_amount_usd' => (float) $invoice?->paid_amount_usd,
            'paid_amount_riel' => (float) $invoice?->paid_amount_riel,
            'total_return' => (float) $invoice?->total_return,
            'total_return_riel' => (float) $invoice?->total_return_riel,
        ];
    }

    /**
     * In stock products, one row per inventory stock.
     *
     * @return Collection<int, Product>
     */
    #[Computed]
    public function products(): Collection
    {
        return Product::query()
            ->branch()
            ->join('inventory_stocks', 'inventory_stocks.inventory_id', '=', 'inventories.id')
            ->where('inventory_stocks.quantity', '>', 0)
            ->when(filled($this->categoryId), fn ($query) => $query->where('inventories.category_id', $this->categoryId))
            ->when(filled($this->locationId), fn ($query) => $query->where('inventory_stocks.location_id', $this->locationId))
            ->when(filled($this->search), fn ($query) => $query->where(fn ($query) => $query
                ->where('inventories.name_kh', 'like', '%'.$this->search.'%')
                ->orWhere('inventories.name', 'like', '%'.$this->search.'%')
                ->orWhere('inventories.pbar_code', $this->search)))
            ->select('inventories.*', 'inventory_stocks.quantity as stock_quantity', 'inventory_stocks.location_id', 'inventory_stocks.id as inventory_stock_id')
            ->with(['metrics', 'media'])
            ->orderBy('inventories.name_kh')
            ->limit((int) env('NUMBER_OF_RECORD', 10))
            ->get();
    }

    /**
     * @return array<int, string>
     */
    #[Computed]
    public function categories(): array
    {
        return Category::query()->branch()->pluck('name_kh', 'id')->all();
    }

    /**
     * @return array<int, string>
     */
    #[Computed]
    public function locations(): array
    {
        return Location::query()->branch()->pluck('name_kh', 'id')->all();
    }

    public function isLocked(): bool
    {
        return in_array($this->invoice?->status, [Invoice::PAID, Invoice::CANCEL]);
    }

    public function addItem(int $stockId): void
    {
        if ($this->isLocked()) {
            $this->notifyError(__('global.invoice_paid_locked'));

            return;
        }

        $metricId = $this->metricId[$stockId] ?? null;

        if (blank($metricId)) {
            $this->notifyError(__('global.select_metric_first'));

            return;
        }

        $qty = $this->qty[$stockId] ?? null;

        if (! ctype_digit((string) $qty) || (int) $qty < 1) {
            $this->notifyError(__('global.invalid_quantity'));

            return;
        }

        $qty = (int) $qty;

        $product = Product::query()
            ->branch()
            ->with('metrics')
            ->find(InventoryStock::query()->find($stockId)?->inventory_id);

        $metric = $product?->metrics->firstWhere('id', (int) $metricId);

        if ($metric === null) {
            $this->notifyError(__('global.select_metric_first'));

            return;
        }

        $price = (float) $metric->pivot->price;

        if ($price <= 0) {
            $this->notifyError(__('global.set_price_first'));

            return;
        }

        $invoice = $this->invoice;
        $branchId = null;
        $exchangeRate = null;

        if ($invoice === null) {
            $branchId = auth()->user()->branch()->first()?->id;

            if ($branchId === null) {
                $this->notifyError(__('global.no_branch_assigned'));

                return;
            }

            $exchangeRate = ExchangeRate::query()->branch()->orderByDesc('id')->first();

            if ($exchangeRate === null) {
                $this->notifyError(__('global.no_exchange_rate'));

                return;
            }
        }

        try {
            $invoice = DB::transaction(function () use ($invoice, $stockId, $metric, $qty, $price, $branchId, $exchangeRate) {
                $stock = InventoryStock::query()->lockForUpdate()->findOrFail($stockId);

                $invoice ??= Invoice::create([
                    'user_id' => auth()->id(),
                    'branch_id' => $branchId,
                    'customer_id' => filled($this->data['customer_id'] ?? null) ? $this->data['customer_id'] : null,
                    'invoiced_at' => now(),
                    'currency' => env('FOREIGN_CURRENCY', 'USD'),
                    'exchange_rate_id' => $exchangeRate->id,
                    'discount' => 0,
                    'total' => 0,
                    'paid_amount' => 0,
                    'status' => Invoice::UNPAID,
                    'order_number' => Invoice::query()->whereDate('invoiced_at', today())->count() + 1,
                ]);

                if (! $stock->take($qty * $metric->qty, 'Sale '.$invoice->invoice_code)) {
                    throw new RuntimeException('The stock could not be updated.');
                }

                $item = $invoice->invoiceItems()
                    ->where('inventory_stock_id', $stockId)
                    ->where('metric_id', $metric->id)
                    ->first();

                if ($item) {
                    $newQty = $item->qty + $qty;

                    $item->update([
                        'qty' => $newQty,
                        'quantity' => $newQty * $metric->qty,
                        'price' => $price,
                        'amount' => InvoiceItem::calculateAmount($newQty, $price, (float) $item->discount, $item->discount_type),
                    ]);
                } else {
                    $invoice->invoiceItems()->create([
                        'user_id' => auth()->id(),
                        'inventory_id' => $stock->inventory_id,
                        'inventory_stock_id' => $stockId,
                        'metric_id' => $metric->id,
                        'qty' => $qty,
                        'quantity' => $qty * $metric->qty,
                        'price' => $price,
                        'discount' => 0,
                        'discount_type' => 'F',
                        'amount' => $qty * $price,
                        'status' => InvoiceItem::UNPAID,
                    ]);
                }

                $invoice->updateTotal();

                return $invoice;
            });
        } catch (NotEnoughStockException) {
            $this->notifyError(__('global.stock_not_enough'));

            return;
        }

        $this->saleId = $invoice->getRouteKey();
        $this->qty[$stockId] = 1;

        $this->refreshInvoice();
    }

    public function removeItem(int $itemId): void
    {
        $item = $this->invoice?->invoiceItems->firstWhere('id', $itemId);

        if ($item === null || $this->isLocked() || $item->status != InvoiceItem::UNPAID) {
            return;
        }

        DB::transaction(function () use ($item) {
            $item->returnToStock('Return stock by removing item from '.$this->invoice->invoice_code);
            $item->forceDelete();
            $this->invoice->updateTotal();
        });

        $this->refreshInvoice();

        Notification::make()->success()->title(__('global.item_deleted'))->send();
    }

    public function setLineDiscount(int $itemId, mixed $discount, string $type): void
    {
        $item = $this->invoice?->invoiceItems->firstWhere('id', $itemId);

        if ($item === null || $this->isLocked()) {
            return;
        }

        $discount = filled($discount) ? $discount : 0;
        $type = $type === 'R' ? 'R' : 'F';
        $gross = $item->qty * $item->price;

        if (! is_numeric($discount) || $discount < 0 || $discount > ($type === 'R' ? 100 : $gross)) {
            $this->notifyError(__('global.discount_more_than_invoice'));
            $this->refreshInvoice();

            return;
        }

        $item->update([
            'discount' => $discount,
            'discount_type' => $type,
            'amount' => InvoiceItem::calculateAmount($item->qty, $item->price, (float) $discount, $type),
        ]);

        $this->invoice->updateTotal();

        $this->refreshInvoice();
    }

    public function applyInvoiceDiscount(): void
    {
        $invoice = $this->invoice;

        if ($invoice === null || $this->isLocked()) {
            return;
        }

        $discount = (float) ($this->data['discount'] ?? 0);

        if ($discount < 0 || $discount > $this->totals['sub_total']) {
            $this->notifyError(__('global.discount_more_than_invoice'));
            $this->data['discount'] = $invoice->discount;

            return;
        }

        $invoice->discount = $discount;
        $invoice->discount_type = 'F';
        $invoice->updateTotal();

        $this->refreshInvoice();
    }

    public function saveCustomer(mixed $customerId): void
    {
        if ($this->invoice === null || $this->isLocked()) {
            return;
        }

        $this->invoice->update([
            'customer_id' => filled($customerId) ? $customerId : null,
            'user_updated' => auth()->id(),
        ]);
    }

    public function payAction(): Action
    {
        return Action::make('pay')
            ->label(__('global.pay'))
            ->icon('heroicon-o-currency-dollar')
            ->color('success')
            ->visible(fn () => $this->invoice !== null
                && $this->invoice->invoiceItems->isNotEmpty()
                && $this->invoice->paid_amount < $this->invoice->total
                && InvoiceResource::userCan('sale:sale:payment'))
            ->modalHeading(__('global.payment'))
            ->modalWidth('2xl')
            ->modalSubmitActionLabel(__('global.save'))
            ->slideOver()
            ->modalSubmitAction(fn ($action) => $action->color('primary'))
            ->modalCancelAction(fn ($action) => $action->color('danger'))
            ->fillForm(fn () => [
                'payment_gateway' => null,
                'total' => round($this->totals['total'], 2),
                'total_in_riels' => round($this->totals['total_in_riel']),
                'payment_amount' => 0,
                'payment_amount_riel' => 0,
                'payment_received' => 0,
                'change_usd' => round(0 - $this->totals['total'], 2),
                'change_riel' => round(0 - $this->totals['total_in_riel']),
            ])
            ->schema([
                Grid::make(1)
                    ->inlineLabel()
                    ->schema([
                        Select::make('payment_gateway')
                            ->label(__('global.payment_gateway'))
                            ->searchable()
                            ->options(fn () => PaymentGateway::query()->pluck('name', 'id')->all())
                            ->placeholder(__('global.select').' '.__('global.payment_gateway'))
                            ->required(),
                        TextInput::make('total')
                            ->label(__('global.total').' ($)')
                            ->readOnly(),
                        TextInput::make('total_in_riels')
                            ->label(__('global.total_in_riels').' (៛)')
                            ->readOnly(),
                        TextInput::make('payment_amount')
                            ->label(__('global.payment_amount'))
                            ->numeric()
                            ->minValue(0)
                            ->live(debounce: 300)
                            ->afterStateUpdated(fn (Get $get, Set $set) => $this->recalculatePayment($get, $set)),
                        TextInput::make('payment_amount_riel')
                            ->label(__('global.payment_amount_riel'))
                            ->numeric()
                            ->minValue(0)
                            ->live(debounce: 300)
                            ->afterStateUpdated(fn (Get $get, Set $set) => $this->recalculatePayment($get, $set)),
                        TextInput::make('payment_received')
                            ->label(__('global.payment_received'))
                            ->readOnly()
                            ->rule(fn (): Closure => function (string $attribute, mixed $value, Closure $fail): void {
                                if (round((float) $value, 2) < round($this->totals['total'], 2)) {
                                    $fail(__('global.payment_less_than_total'));
                                }
                            }),
                        TextInput::make('change_usd')
                            ->label(__('global.change_usd'))
                            ->readOnly(),
                        TextInput::make('change_riel')
                            ->label(__('global.change_riel'))
                            ->readOnly(),
                    ]),

            ])
            ->action(function (array $data): void {
                $this->invoice->recordPayment(
                    (float) ($data['payment_amount'] ?? 0),
                    (float) ($data['payment_amount_riel'] ?? 0),
                    (int) $data['payment_gateway'],
                );

                $this->refreshInvoice();

                Notification::make()->success()->title(__('global.payment_success'))->send();
            });
    }

    protected function recalculatePayment(Get $get, Set $set): void
    {
        $exchangeRate = $this->totals['exchange_rate'];
        $usd = (float) $get('payment_amount');
        $riel = (float) $get('payment_amount_riel');

        $received = $usd + ($exchangeRate > 0 ? $riel / $exchangeRate : 0);

        $set('payment_received', round($received, 2));
        $set('change_usd', round($received - $this->totals['total'], 2));
        $set('change_riel', round(($received - $this->totals['total']) * $exchangeRate));
    }

    /**
     * Drop everything derived from stock or the invoice so the next render reads fresh values.
     *
     * Changing the qty input fires Filament's schema update hooks, which build the metric selects and
     * therefore memoize $this->products before the stock is taken. The metric select schema is dropped
     * too, so its components match the refreshed product list.
     */
    protected function refreshInvoice(): void
    {
        unset($this->invoice, $this->totals, $this->products);

        unset($this->cachedSchemas['metricSelectForm']);

        $this->data['discount'] = $this->invoice->discount ?? 0;
        $this->data['invoiced_at'] = ($this->invoice->invoiced_at ?? now())->format('d/m/Y');
    }

    /**
     * The first 50 customers of the branch, narrowed down by the search text when there is one.
     *
     * @return array<int, string>
     */
    protected function customerOptions(?string $search = null): array
    {
        return Customer::query()
            ->branch()
            ->when(filled($search), fn ($query) => $query
                ->where(fn ($query) => $query
                    ->where('name_kh', 'like', "%{$search}%")
                    ->orWhere('cus_code', 'like', "%{$search}%")
                    ->orWhere('phone_number', 'like', "%{$search}%")))
            ->limit(50)
            ->get()
            ->mapWithKeys(fn (Customer $customer) => [$customer->id => $this->customerLabel($customer)])
            ->all();
    }

    protected function customerLabel(Customer $customer): string
    {
        return implode(' - ', array_filter([$customer->name_kh, $customer->cus_code, $customer->phone_number]));
    }

    protected function notifyError(string $message): void
    {
        Notification::make()->danger()->title($message)->send();
    }
}
