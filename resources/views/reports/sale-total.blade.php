<!DOCTYPE html>
<html lang="km">
@include('reports.partials.print-head', ['title' => $detail ? __('global.report_sale_total_detail') : __('global.report_sale_total')])
<body>
    <div class="close_btn" style="text-align: center; margin-top: 10px;">
        <a href="{{ \App\Filament\Pages\Reports::getUrl() }}" class="button-link">{{ __('global.close') }}</a>
    </div>

    @include('reports.partials.print-header', ['title' => $detail ? __('global.report_sale_total_detail') : __('global.report_sale_total')])

    <table class="table_1">
        @php $grandTotal = 0; @endphp
        @forelse ($resultsGroupBy as $group)
            @php
                $dayInvoices = $results->where('payment_date', $group->payment_date)->values();
                $dayTotal = $dayInvoices->where('status', \App\Models\Invoice::PAID)->sum('total');
                $grandTotal += $dayTotal;
            @endphp
            <tr>
                <td class="content_regular txt_bold" colspan="{{ $detail ? 7 : 5 }}">
                    {{ __('global.date') }} : {{ \Illuminate\Support\Carbon::parse($group->payment_date)->format('d-m-Y') }}
                </td>
            </tr>

            @if ($detail)
                @foreach ($dayInvoices as $invoice)
                    <tr>
                        <td class="content_regular txt_left txt_bold {{ $invoice->status === \App\Models\Invoice::CANCEL ? 'txt_danger' : '' }}" colspan="7">
                            @if ($invoice->status === \App\Models\Invoice::CANCEL)
                                {{ __('global.cancelled') }} —
                            @endif
                            {{ $invoice->invoice_code }} — {{ number_format($invoice->total, 2) }} $
                            @if ($invoice->discount)
                                ({{ __('global.discount') }}: {{ number_format($invoice->discount, 2) }} $)
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td width="5%" class="khmer_moul_title_1">ល.រ</td>
                        <td width="30%" class="khmer_moul_title_1">{{ __('global.pname_kh') }}</td>
                        <td class="khmer_moul_title_1">{{ __('global.sale_qty') }}</td>
                        <td class="khmer_moul_title_1">{{ __('global.sale_price') }}</td>
                        <td class="khmer_moul_title_1">{{ __('global.discount') }}</td>
                        <td class="khmer_moul_title_1">{{ __('global.total') }}</td>
                        <td width="10%" class="khmer_moul_title_1"></td>
                    </tr>
                    @foreach ($invoice->invoiceItems as $key => $item)
                        @php
                            $itemDiscount = $item->discount_type === 'F' ? $item->discount : ($item->amount * ($item->discount / 100));
                        @endphp
                        <tr>
                            <td class="content_regular">{{ $key + 1 }}</td>
                            <td class="content_regular txt_left">{{ $item->product?->name_kh }}</td>
                            <td class="content_regular">{{ $item->qty }}</td>
                            <td class="content_regular">{{ number_format($item->price, 2) }} $</td>
                            <td class="content_regular">{{ $itemDiscount ? number_format($itemDiscount, 2).' $' : '' }}</td>
                            <td class="content_regular">{{ number_format($item->amount, 2) }} $</td>
                            <td class="content_regular"></td>
                        </tr>
                    @endforeach
                @endforeach
                <tr>
                    <td class="content_regular txt_right txt_bold" colspan="5">{{ __('global.total_sale') }}</td>
                    <td class="content_regular txt_right txt_bold">{{ number_format($dayTotal, 2) }} $</td>
                    <td></td>
                </tr>
            @else
                <tr>
                    <td width="5%" class="khmer_moul_title_1">ល.រ</td>
                    <td width="40%" class="khmer_moul_title_1">{{ __('global.invoice_code') }}</td>
                    <td width="15%" class="khmer_moul_title_1">{{ __('global.discount') }}</td>
                    <td width="20%" class="khmer_moul_title_1">{{ __('global.total') }}</td>
                    <td width="10%" class="khmer_moul_title_1"></td>
                </tr>
                @foreach ($dayInvoices as $key => $invoice)
                    <tr>
                        <td class="content_regular {{ $invoice->status === \App\Models\Invoice::CANCEL ? 'txt_danger' : '' }}">{{ $key + 1 }}</td>
                        <td class="content_regular txt_left {{ $invoice->status === \App\Models\Invoice::CANCEL ? 'txt_danger' : '' }}">{{ $invoice->invoice_code }}</td>
                        <td class="content_regular {{ $invoice->status === \App\Models\Invoice::CANCEL ? 'txt_danger' : '' }}">{{ $invoice->discount ? number_format($invoice->discount, 2).' $' : '' }}</td>
                        <td class="content_regular {{ $invoice->status === \App\Models\Invoice::CANCEL ? 'txt_danger' : '' }}">{{ number_format($invoice->total, 2) }} $</td>
                        <td class="content_regular"></td>
                    </tr>
                @endforeach
                <tr>
                    <td class="content_regular txt_right txt_bold" colspan="3">{{ __('global.total_sale') }}</td>
                    <td class="content_regular txt_right txt_bold">{{ number_format($dayTotal, 2) }} $</td>
                    <td></td>
                </tr>
            @endif
        @empty
            <tr>
                <td class="content_regular">{{ __('global.report_no_data') }}</td>
            </tr>
        @endforelse

        @if ($resultsGroupBy->isNotEmpty())
            <tr>
                <td class="content_regular txt_right txt_bold" style="font-size: 14pt;" colspan="{{ $detail ? 5 : 3 }}">{{ __('global.total_sale') }}</td>
                <td class="content_regular txt_right txt_bold" style="font-size: 14pt;">{{ number_format($grandTotal, 2) }} $</td>
                <td></td>
            </tr>
        @endif
    </table>

    <script>
        window.addEventListener('load', function () {
            window.print();
        });
    </script>
</body>
</html>
