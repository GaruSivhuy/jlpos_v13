<x-filament-panels::page>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        {{-- ===================== LEFT: INVOICE ===================== --}}
        <div class="lg:col-span-5 space-y-4">

            <div class="flex justify-between items-center">
                <div>
                    <span class="font-medium">{{ __('global.invoice_no') }}:</span>
                    <span class="text-lg font-bold">{{ $this->invoice?->invoice_code }}</span>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-medium">{{ __('customer::customer.customer') }}</label>
                    <select wire:model.live="customerId" class="fi-select-input w-full rounded-lg border-gray-300">
                        <option value="">{{ __('sale::sale.select_customer') }}</option>
                        @foreach($this->customers as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-sm font-medium">{{ __('sale::sale.date') }}</label>
                    <input type="text" readonly value="{{ $invoicedAt }}"
                           class="fi-input w-full rounded-lg border-gray-300" />
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-medium">
                        {{ __('sale::sale.discount') }} ({{ env('FOREIGN_CURRENCY_SIGN', '$') }})
                    </label>
                    <input type="text" wire:model.blur="discountToAll" wire:blur="setInvoiceDiscount"
                           class="fi-input w-full rounded-lg border-gray-300" />
                </div>
                <div>
                    <label class="text-sm font-medium">{{ __('sale::sale.sale_by') }}</label>
                    <input type="text" readonly value="{{ auth()->user()->name }}"
                           class="fi-input w-full rounded-lg border-gray-300" />
                </div>
            </div>

            <table class="w-full text-sm border">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="p-2 text-center">{{ __('global.action') }}</th>
                        <th class="p-2 text-center">{{ __('global.id') }}</th>
                        <th class="p-2 text-center">{{ __('sale::sale.name') }}</th>
                        <th class="p-2 text-center">{{ __('product::product.qty') }}</th>
                        <th class="p-2 text-center">{{ __('product::product.price_whole') }}</th>
                        <th class="p-2 text-center">{{ __('sale::sale.discount') }}</th>
                        <th class="p-2 text-center">{{ __('sale::sale.total') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($this->invoice?->invoiceItems ?? [] as $item)
                        <tr wire:key="invoice-item-{{ $item->id }}">
                            <td class="p-2 text-center">
                                <button type="button" class="text-danger-600"
                                        wire:click="removeItem({{ $item->id }})"
                                        wire:confirm="{{ __('global.are_you_sure') }}">
                                    <x-heroicon-o-trash class="w-4 h-4" />
                                </button>
                            </td>
                            <td class="p-2 text-center">{{ $item->id }}</td>
                            <td class="p-2">{{ $item->product_name }}</td>
                            <td class="p-2 text-center">{{ $item->qty }}</td>
                            <td class="p-2 text-right">{{ $item->price }}</td>
                            <td class="p-2 text-right">
                                <input type="text" value="{{ $item->discount }}"
                                       wire:blur="setLineDiscount({{ $item->id }}, $event.target.value)"
                                       class="fi-input w-20 rounded border-gray-300 text-right" />
                            </td>
                            <td class="p-2 text-right">{{ $item->total }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-3 text-center text-danger-600 italic">
                                {{ __('sale::sale.no_item') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3" class="p-2 text-right">
                            {{ __('sale::sale.sub_total') }} ({{ env('FOREIGN_CURRENCY_SIGN', '$') }})
                        </th>
                        <th class="p-2 text-right">{{ $this->invoiceTotals['sub_total'] }}</th>
                        <th colspan="2" class="p-2 text-right">
                            {{ __('sale::sale.exchange_rate') }} {{ __('sale::sale.1usd') }}
                        </th>
                        <th class="p-2 text-right">{{ $this->invoiceTotals['exchange_rate'] }}</th>
                    </tr>
                    <tr>
                        <th colspan="3" class="p-2 text-right">
                            {{ __('sale::sale.discount') }} ({{ env('FOREIGN_CURRENCY_SIGN', '$') }})
                        </th>
                        <th class="p-2 text-right">{{ $this->invoiceTotals['discount'] }}</th>
                        <th colspan="3"></th>
                    </tr>
                    <tr>
                        <th colspan="3" class="p-2 text-right">
                            {{ __('sale::sale.total') }} ({{ env('FOREIGN_CURRENCY_SIGN', '$') }})
                        </th>
                        <th class="p-2 text-right">{{ $this->invoiceTotals['total'] }}</th>
                        <th colspan="2" class="p-2 text-right">
                            {{ __('sale::sale.total_in_riels') }} ({{ env('LOCAL_CURRENCY', '៛') }})
                        </th>
                        <th class="p-2 text-right">{{ $this->invoiceTotals['total_in_riel'] }}</th>
                    </tr>
                    @if($this->invoice?->invoicePayments->isNotEmpty())
                        <tr>
                            <th colspan="3" class="p-2 text-right">{{ __('sale::sale.total_paid') }} ($)</th>
                            <th class="p-2 text-right">{{ $this->invoiceTotals['paid_amount_usd'] }}</th>
                            <th colspan="2" class="p-2 text-right">{{ __('sale::sale.total_paid') }} (៛)</th>
                            <th class="p-2 text-right">{{ $this->invoiceTotals['paid_amount_riel'] }}</th>
                        </tr>
                        <tr>
                            <th colspan="3" class="p-2 text-right">{{ __('sale::sale.changed') }} ($)</th>
                            <th class="p-2 text-right">{{ $this->invoiceTotals['total_return'] }}</th>
                            <th colspan="2" class="p-2 text-right">{{ __('sale::sale.changed') }} (៛)</th>
                            <th class="p-2 text-right">{{ $this->invoiceTotals['total_return_riel'] }}</th>
                        </tr>
                    @endif
                </tfoot>
            </table>

            <div class="flex justify-end gap-2">
                @if($this->invoice && auth()->user()->can('sale:sale:payment'))
                    @if($this->invoice->paid_amount < $this->invoice->total)
                        <x-filament::button color="success" icon="heroicon-o-currency-dollar"
                                             wire:click="$dispatch('open-modal', { id: 'payment-modal' })">
                            {{ __('sale::sale.pay') }}
                        </x-filament::button>
                    @endif

                    @if($this->invoice->invoiceItems->isNotEmpty())
                        <x-filament::button tag="a" color="info" icon="heroicon-o-printer"
                            href="{{ route('sale.menu.pos.printReceipt', ['sale_id' => \App\Http\Controllers\Controller::encryptShort($this->invoice->id)]) }}"
                            target="_blank">
                            {{ __('sale::sale.print') }}
                        </x-filament::button>
                    @endif

                    <x-filament::button tag="a" color="danger" icon="heroicon-o-x-mark"
                                         href="{{ route('sale.menu.pos') }}">
                        {{ __('component.cancel') }}
                    </x-filament::button>
                @endif
            </div>
        </div>

        {{-- ===================== RIGHT: PRODUCT PICKER ===================== --}}
        <div class="lg:col-span-7 space-y-4">
            <div class="grid grid-cols-3 gap-2">
                <select wire:model.live="locationId" class="fi-select-input rounded-lg border-gray-300">
                    <option value="">{{ __('sale::sale.all') }}</option>
                    @foreach($this->locations as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>

                <select wire:model.live="categoryId" class="fi-select-input rounded-lg border-gray-300">
                    <option value="">{{ __('sale::sale.all') }}</option>
                    @foreach($this->categories as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>

                <input type="text" wire:model.live.debounce.400ms="search"
                       placeholder="{{ __('sale::sale.search') }}"
                       class="fi-input rounded-lg border-gray-300" />
            </div>

            <table class="w-full text-sm border table-fixed">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="p-2 text-center w-1/6">{{ __('sale::sale.photo') }}</th>
                        <th class="p-2 text-center w-1/4">{{ __('global.name_kh') }}</th>
                        <th class="p-2 text-center">{{ __('product::product.mname') }}</th>
                        <th class="p-2 text-center w-1/12">{{ __('sale::sale.in_stock') }}</th>
                        <th class="p-2 text-center w-1/12">{{ __('product::product.price_whole') }}</th>
                        <th class="p-2 text-center w-1/6">{{ __('global.action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($this->products as $item)
                        <tr wire:key="product-{{ $item->inventory_stock_id }}">
                            <td class="p-2 text-center">
                                <img height="60" src="{{ $item->image_url ?? asset('backend/img/no-image.png') }}" />
                            </td>
                            <td class="p-2">{!! $item->name_kh !!}</td>
                            <td class="p-2 text-center">
                                <select wire:model="metricId.{{ $item->inventory_stock_id }}"
                                        wire:change="updatedMetricId($event.target.value, {{ $item->inventory_stock_id }})"
                                        class="fi-select-input w-full rounded border-gray-300">
                                    <option value="">សូមជ្រើសរើស</option>
                                    @foreach($item->metrics->pluck('name_kh', 'id') as $mid => $mname)
                                        <option value="{{ $mid }}">{{ $mname }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="p-2 text-center">{{ $item->quantity ?? 0 }}</td>
                            <td class="p-2 text-right">
                                {{ $item->price ?? 0 }}{{ env('FOREIGN_CURRENCY_SIGN', '$') }}
                            </td>
                            <td class="p-2 text-right">
                                <div class="flex items-center gap-1">
                                    <input type="text"
                                           wire:model="qty.{{ $item->inventory_stock_id }}"
                                           value="1"
                                           class="fi-input w-14 rounded border-gray-300"
                                           @if($this->invoice && $this->invoice->status == \HUY\Sale\Entities\Invoice::PAID) disabled @endif />
                                    @if(!$this->invoice || $this->invoice->status != \HUY\Sale\Entities\Invoice::PAID)
                                        <x-filament::icon-button icon="heroicon-o-plus" color="primary"
                                            wire:click="addItem({{ $item->inventory_stock_id }})" />
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</x-filament-panels::page>