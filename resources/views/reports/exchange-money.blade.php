<!DOCTYPE html>
<html lang="km">
@include('reports.partials.print-head', ['title' => __('global.report_exchange_money')])
<body>
    <div class="close_btn" style="text-align: center; margin-top: 10px;">
        <a href="{{ \App\Filament\Pages\Reports::getUrl() }}" class="button-link">{{ __('global.close') }}</a>
    </div>

    @include('reports.partials.print-header', ['title' => __('global.report_exchange_money')])

    <table class="table_1">
        @forelse ($resultsGroupBy as $group)
            @php $dayTypes = $resultsType->where('exchange_date', $group->exchange_date)->values(); @endphp
            <tr>
                <td class="content_regular txt_bold" colspan="8">
                    {{ __('global.date') }} : {{ \Illuminate\Support\Carbon::parse($group->exchange_date)->format('d-m-Y') }}
                </td>
            </tr>

            @foreach ($dayTypes as $type)
                @php
                    $rows = $results
                        ->where('exchange_type', $type->exchange_type)
                        ->where('exchange_date', $type->exchange_date)
                        ->values();
                    $subTotalAmount = 0;
                    $subTotalSystem = 0;
                    $totalProfit = 0;
                    $isUsdToKhr = $type->exchange_type === \App\Models\ExchangeMoney::USD_TO_KHR;
                @endphp
                <tr>
                    <td class="content_regular txt_left txt_bold" colspan="8">
                        {{ __('global.exchange_type') }} : {{ $isUsdToKhr ? __('global.exchange_usd_to_khr') : __('global.exchange_khr_to_usd') }}
                    </td>
                </tr>
                <tr>
                    <td width="5%" class="khmer_moul_title_1">ល.រ</td>
                    <td width="15%" class="khmer_moul_title_1">{{ __('global.amount') }}</td>
                    <td width="15%" class="khmer_moul_title_1">{{ __('global.exchange_rate') }}</td>
                    <td width="20%" class="khmer_moul_title_1">{{ __('global.total_amount') }}</td>
                    <td width="15%" class="khmer_moul_title_1">{{ __('global.exchange_rate_1_usd') }}</td>
                    <td width="15%" class="khmer_moul_title_1">{{ __('global.report_system_total') }}</td>
                    <td width="10%" class="khmer_moul_title_1">{{ __('global.report_profit') }}</td>
                    <td width="5%" class="khmer_moul_title_1"></td>
                </tr>
                @foreach ($rows as $key => $item)
                    @php
                        $exchangeRate = $item->exchange?->exchange_rate ?: 4100;
                        $totalSystem = $isUsdToKhr
                            ? $item->amount_exchange * $exchangeRate
                            : $item->amount_exchange / $exchangeRate;
                        $profit = $isUsdToKhr
                            ? $totalSystem - ($item->amount_exchange * $item->rate_exchange)
                            : $totalSystem - ($item->amount_exchange / $item->rate_exchange);

                        $subTotalAmount += $item->total_amount;
                        $subTotalSystem += $totalSystem;
                        $totalProfit += $profit;
                    @endphp
                    <tr>
                        <td class="content_regular">{{ $key + 1 }}</td>
                        <td class="content_regular txt_left">
                            {{ $isUsdToKhr ? number_format($item->amount_exchange, 2).' $' : number_format($item->amount_exchange).' ៛' }}
                        </td>
                        <td class="content_regular">{{ number_format($item->rate_exchange) }} ៛</td>
                        <td class="content_regular">
                            {{ $isUsdToKhr ? number_format($item->total_amount).' ៛' : number_format($item->total_amount, 2).' $' }}
                        </td>
                        <td class="content_regular">{{ number_format($exchangeRate) }} ៛</td>
                        <td class="content_regular">
                            {{ $isUsdToKhr ? number_format($totalSystem).' ៛' : number_format($totalSystem, 2).' $' }}
                        </td>
                        <td class="content_regular">
                            {{ $isUsdToKhr ? number_format($profit).' ៛' : number_format($profit, 2).' $' }}
                        </td>
                        <td class="content_regular"></td>
                    </tr>
                @endforeach
                <tr>
                    <td class="content_regular txt_right txt_bold" colspan="3">{{ __('global.total_sale') }}</td>
                    <td class="content_regular txt_right txt_bold">
                        {{ $isUsdToKhr ? number_format($subTotalAmount).' ៛' : number_format($subTotalAmount, 2).' $' }}
                    </td>
                    <td class="content_regular txt_right txt_bold">{{ __('global.report_system_total') }}</td>
                    <td class="content_regular txt_right txt_bold">
                        {{ $isUsdToKhr ? number_format($subTotalSystem).' ៛' : number_format($subTotalSystem, 2).' $' }}
                    </td>
                    <td class="content_regular txt_right txt_bold">
                        {{ $isUsdToKhr ? number_format($totalProfit).' ៛' : number_format($totalProfit, 2).' $' }}
                    </td>
                    <td></td>
                </tr>
            @endforeach
        @empty
            <tr>
                <td class="content_regular">{{ __('global.report_no_data') }}</td>
            </tr>
        @endforelse
    </table>

    <script>
        window.addEventListener('load', function () {
            window.print();
        });
    </script>
</body>
</html>
