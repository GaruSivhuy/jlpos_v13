<x-filament-panels::page>
    @php
        $invoice = $this->invoice;
        $totals = $this->totals;
        $locked = $this->isLocked();
        $sign = env('FOREIGN_CURRENCY_SIGN', '$');
        $localSign = env('LOCAL_CURRENCY', '៛');
        $trimNumber = fn ($value) => rtrim(rtrim(number_format((float) $value, 2, '.', ''), '0'), '.');
    @endphp

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-[43fr_57fr]">

        {{-- ===================== LEFT: INVOICE ===================== --}}
        <div class="min-w-0 space-y-4">
            <x-filament::section>
                <div class="mb-4 flex items-center gap-2">
                    <span class="text-sm font-medium text-gray-500">{{ __('global.invoice_code') }} :</span>
                    <span class="text-xl font-bold">{{ $invoice?->invoice_code }}</span>
                </div>

                {{ $this->form }}
            </x-filament::section>

            <x-filament::section :compact="true">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 text-gray-500">
                                <th class="p-2 text-center">{{ __('global.action') }}</th>
                                <th class="p-2 text-center">{{ __('global.id') }}</th>
                                <th class="p-2 text-left">{{ __('global.pname_kh') }}</th>
                                <th class="p-2 text-center">{{ __('global.sale_qty') }}</th>
                                <th class="p-2 text-right">{{ __('global.sale_price') }}</th>
                                <th class="p-2 text-center">{{ __('global.discount') }}</th>
                                <th class="p-2 text-right">{{ __('global.total') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($invoice?->invoiceItems ?? [] as $item)
                                <tr wire:key="invoice-item-{{ $item->id }}">
                                    <td class="p-2 text-center">
                                        @if (! $locked && $item->status == \App\Models\InvoiceItem::UNPAID)
                                            <x-filament::icon-button
                                                icon="heroicon-o-trash"
                                                color="danger"
                                                size="sm"
                                                wire:click="removeItem({{ $item->id }})"
                                                wire:confirm="{{ __('global.are_you_sure') }}"
                                            />
                                        @endif
                                    </td>
                                    <td class="p-2 text-center">{{ $loop->iteration }}</td>
                                    <td class="p-2">
                                        {{ $item->product?->name_kh }}
                                        @if ($item->metric)
                                            <br><span class="text-xs text-gray-500">({{ $item->metric->name_kh }})</span>
                                        @endif
                                    </td>
                                    <td class="p-2 text-center">{{ $item->qty }}</td>
                                    <td class="p-2 text-right">{{ number_format($item->price, 2) }}</td>
                                    <td class="p-2">
                                        <div
                                            x-data="{ discount: @js($item->discount), type: @js($item->discount_type ?: 'F') }"
                                            class="flex items-center justify-center gap-1"
                                        >
                                            <x-filament::input.wrapper class="w-20">
                                                <x-filament::input.select
                                                    x-model="type"
                                                    x-on:change="$wire.setLineDiscount({{ $item->id }}, discount, type)"
                                                    :disabled="$locked"
                                                >
                                                    <option value="R">%</option>
                                                    <option value="F">{{ $sign }}</option>
                                                </x-filament::input.select>
                                            </x-filament::input.wrapper>
                                            <x-filament::input.wrapper class="w-20">
                                                <x-filament::input
                                                    type="text"
                                                    x-model="discount"
                                                    x-on:change="$wire.setLineDiscount({{ $item->id }}, discount, type)"
                                                    :disabled="$locked"
                                                />
                                            </x-filament::input.wrapper>
                                        </div>
                                    </td>
                                    <td class="p-2 text-right">{{ number_format($item->amount, 2) }} {{ $sign }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="p-3 text-center text-xs italic text-danger-600">
                                        {{ __('global.no_item') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="border-t border-gray-200 font-semibold">
                            <tr>
                                <td colspan="3" class="p-2 text-right">{{ __('global.sub_total') }} ({{ $sign }})</td>
                                <td colspan="2" class="p-2 text-right">{{ number_format($totals['sub_total'], 2) }}</td>
                                <td class="p-2 text-right">{{ __('global.exchange_rate_per_usd') }}</td>
                                <td class="p-2 text-right">{{ number_format($totals['exchange_rate'], 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="p-2 text-right">{{ __('global.discount') }} ({{ $sign }})</td>
                                <td colspan="2" class="p-2 text-right">{{ number_format($totals['discount'], 2) }}</td>
                                <td colspan="2"></td>
                            </tr>
                            <tr>
                                <td colspan="3" class="p-2 text-right">{{ __('global.total') }} ({{ $sign }})</td>
                                <td colspan="2" class="p-2 text-right">{{ number_format($totals['total'], 2) }}</td>
                                <td class="p-2 text-right">{{ __('global.total_in_riels') }} ({{ $localSign }})</td>
                                <td class="p-2 text-right">{{ number_format($totals['total_in_riel']) }}</td>
                            </tr>
                            @if ($invoice?->invoicePayments->isNotEmpty())
                                <tr>
                                    <td colspan="3" class="p-2 text-right">{{ __('global.total_paid') }} ({{ $sign }})</td>
                                    <td colspan="2" class="p-2 text-right">{{ number_format($totals['paid_amount_usd'], 2) }}</td>
                                    <td class="p-2 text-right">{{ __('global.total_paid') }} ({{ $localSign }})</td>
                                    <td class="p-2 text-right">{{ number_format($totals['paid_amount_riel']) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="p-2 text-right">{{ __('global.changed') }} ({{ $sign }})</td>
                                    <td colspan="2" class="p-2 text-right">{{ number_format($totals['total_return'], 2) }}</td>
                                    <td class="p-2 text-right">{{ __('global.changed') }} ({{ $localSign }})</td>
                                    <td class="p-2 text-right">{{ number_format($totals['total_return_riel']) }}</td>
                                </tr>
                            @endif
                        </tfoot>
                    </table>
                </div>

                <div class="mt-4 flex flex-wrap justify-end gap-2">
                    {{ $this->payAction }}

                    @if ($invoice && $invoice->invoiceItems->isNotEmpty())
                        <x-filament::button
                            tag="a"
                            color="info"
                            icon="heroicon-o-printer"
                            :href="route('sale.receipt', ['sale_id' => $invoice->getRouteKey()])"
                            target="_blank"
                        >
                            {{ __('global.print') }}
                        </x-filament::button>
                    @endif

                    @if ($invoice)
                        <x-filament::button
                            tag="a"
                            color="danger"
                            icon="heroicon-o-x-mark"
                            :href="\App\Filament\Resources\Sale\InvoiceResource::getUrl('pos')"
                        >
                            {{ __('global.cancel') }}
                        </x-filament::button>
                    @endif
                </div>
            </x-filament::section>
        </div>

        {{-- ===================== RIGHT: PRODUCT PICKER ===================== --}}
        <div class="min-w-0 space-y-4">
            <div class="grid grid-cols-1 gap-2 sm:grid-cols-4">
                <div class="grid grid-cols-1 gap-2 sm:col-span-3 sm:grid-cols-2">
                    {{ $this->locationFilterForm }}

                    {{ $this->categoryFilterForm }}
                </div>

                <x-filament::input.wrapper prefix-icon="heroicon-m-magnifying-glass">
                    <x-filament::input
                        type="text"
                        wire:model.live.debounce.400ms="search"
                        :placeholder="__('global.search')"
                    />
                </x-filament::input.wrapper>
            </div>

            @php
                $metricSelects = $this->metricSelectForm->getFlatComponents();
            @endphp

            <x-filament::section :compact="true">
                {{-- Fixed positioning keeps the searchable select panels from being clipped by the scroll container. --}}
                <div class="fi-fixed-positioning-context overflow-x-auto">
                    <table class="w-full table-fixed text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 text-gray-500">
                                <th class="w-20 p-2 text-center">{{ __('global.photo') }}</th>
                                <th class="p-2 text-left">{{ __('global.pname_kh') }}</th>
                                <th class="w-40 p-2 text-center">{{ __('global.mname') }}</th>
                                <th class="w-20 p-2 text-center">{{ __('global.in_stock') }}</th>
                                <th class="w-20 p-2 text-right">{{ __('global.sale_price') }}</th>
                                <th class="w-36 p-2 text-center">{{ __('global.action') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($this->products as $product)
                                @php
                                    $stockId = $product->inventory_stock_id;
                                    $selectedMetric = filled($this->metricId[$stockId] ?? null)
                                        ? $product->metrics->firstWhere('id', (int) $this->metricId[$stockId])
                                        : null;
                                    $price = $selectedMetric ? (float) $selectedMetric->pivot->price : 0;
                                    $inStock = $selectedMetric && $selectedMetric->qty > 0 ? $product->stock_quantity / $selectedMetric->qty : 0;
                                    $image = $product->getFirstMediaUrl('product');
                                @endphp
                                <tr wire:key="product-{{ $stockId }}">
                                    <td class="p-2 text-center">
                                        @if ($image)
                                            <img src="{{ $image }}" alt="" class="mx-auto h-14 w-14 rounded object-cover" />
                                        @else
                                            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded bg-gray-100 text-gray-400">
                                                <x-filament::icon icon="heroicon-o-photo" class="h-6 w-6" />
                                            </div>
                                        @endif
                                    </td>
                                    <td class="p-2">{{ $product->name_kh }}</td>
                                    <td class="p-2">
                                        {{ $metricSelects['metricId.'.$stockId] }}
                                    </td>
                                    <td class="p-2 text-center">{{ $trimNumber($inStock) }}</td>
                                    <td class="p-2 text-right">{{ $trimNumber($price) }}{{ $sign }}</td>
                                    <td class="p-2">
                                        <div class="flex items-center justify-center gap-1">
                                            <x-filament::input.wrapper class="w-20">
                                                <x-filament::input
                                                    type="number"
                                                    min="1"
                                                    step="1"
                                                    placeholder="1"
                                                    wire:model="qty.{{ $stockId }}"
                                                    :disabled="$locked"
                                                />
                                            </x-filament::input.wrapper>
                                            @unless ($locked)
                                                <x-filament::icon-button
                                                    icon="heroicon-o-plus"
                                                    color="primary"
                                                    wire:click="addItem({{ $stockId }})"
                                                    wire:loading.attr="disabled"
                                                />
                                            @endunless
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-4 text-center text-gray-500">{{ __('global.no_record_found') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-filament::section>
        </div>
    </div>
</x-filament-panels::page>
