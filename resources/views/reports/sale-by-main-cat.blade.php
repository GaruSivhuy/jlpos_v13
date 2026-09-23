<!DOCTYPE html>
<html lang="km">
@include('reports.partials.print-head', ['title' => __('global.report_sale_by_main_cat_detail')])
<body>
    <div class="close_btn" style="text-align: center; margin-top: 10px;">
        <a href="{{ \App\Filament\Pages\Reports::getUrl() }}" class="button-link">{{ __('global.close') }}</a>
    </div>

    @include('reports.partials.print-header', ['title' => __('global.report_sale_by_main_cat_detail').($mainCategory ? ' — '.$mainCategory->cat_name_kh : '')])

    <table class="table_1">
        @php $grandTotal = 0; @endphp
        @forelse ($resultsGroupBy as $group)
            @php $dayGateways = $resultsGateway->where('payment_date', $group->payment_date)->values(); @endphp
            <tr>
                <td class="content_regular txt_bold" colspan="7">
                    {{ __('global.date') }} : {{ \Illuminate\Support\Carbon::parse($group->payment_date)->format('d-m-Y') }}
                </td>
            </tr>

            @foreach ($dayGateways as $gateway)
                @php
                    $rows = $results
                        ->where('payment_gateway', $gateway->payment_gateway)
                        ->where('payment_date', $gateway->payment_date)
                        ->values();
                    $gatewayTotal = $rows->where('status', \App\Models\Invoice::PAID)->sum('amount');
                    $grandTotal += $gatewayTotal;
                @endphp
                <tr>
                    <td class="content_regular txt_left txt_bold" colspan="7">
                        {{ __('global.payment_gateway') }} : {{ $gateway->paymentGateway?->name }}
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
                @foreach ($rows as $key => $row)
                    @php
                        $discount = $row->discount_type === 'F' ? $row->discount : ($row->amount * ($row->discount / 100));
                        $invoiceCode = 'INV-'.str_pad((string) $row->invoice_id, (int) env('ID_PAD_LENGTH', 8), '0', STR_PAD_LEFT);
                    @endphp
                    <tr>
                        <td class="content_regular {{ $row->status === \App\Models\Invoice::CANCEL ? 'txt_danger' : '' }}">{{ $key + 1 }}</td>
                        <td class="content_regular txt_left {{ $row->status === \App\Models\Invoice::CANCEL ? 'txt_danger' : '' }}">{{ $row->name_kh }} — {{ $invoiceCode }}</td>
                        <td class="content_regular {{ $row->status === \App\Models\Invoice::CANCEL ? 'txt_danger' : '' }}">{{ $row->qty }}</td>
                        <td class="content_regular {{ $row->status === \App\Models\Invoice::CANCEL ? 'txt_danger' : '' }}">{{ number_format($row->price, 2) }} $</td>
                        <td class="content_regular {{ $row->status === \App\Models\Invoice::CANCEL ? 'txt_danger' : '' }}">{{ $discount ? number_format($discount, 2).' $' : '' }}</td>
                        <td class="content_regular {{ $row->status === \App\Models\Invoice::CANCEL ? 'txt_danger' : '' }}">{{ number_format($row->amount, 2) }} $</td>
                        <td class="content_regular"></td>
                    </tr>
                @endforeach
                <tr>
                    <td class="content_regular txt_right txt_bold" colspan="5">{{ __('global.total_sale') }}</td>
                    <td class="content_regular txt_right txt_bold">{{ number_format($gatewayTotal, 2) }} $</td>
                    <td></td>
                </tr>
            @endforeach
        @empty
            <tr>
                <td class="content_regular">{{ __('global.report_no_data') }}</td>
            </tr>
        @endforelse

        @if ($resultsGroupBy->isNotEmpty())
            <tr>
                <td class="content_regular txt_right txt_bold" style="font-size: 14pt;" colspan="5">{{ __('global.total_sale') }}</td>
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
