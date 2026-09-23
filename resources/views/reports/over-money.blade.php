<!DOCTYPE html>
<html lang="km">
@include('reports.partials.print-head', ['title' => __('global.report_over_money')])
<body>
    <div class="close_btn" style="text-align: center; margin-top: 10px;">
        <a href="{{ \App\Filament\Pages\Reports::getUrl() }}" class="button-link">{{ __('global.close') }}</a>
    </div>

    @include('reports.partials.print-header', ['title' => __('global.report_over_money')])

    <table class="table_1">
        @forelse ($resultsGroupBy as $group)
            @php $dayTypes = $resultsType->where('over_money_date', $group->over_money_date)->values(); @endphp
            <tr>
                <td class="content_regular txt_bold" colspan="3">
                    {{ __('global.date') }} : {{ \Illuminate\Support\Carbon::parse($group->over_money_date)->format('d-m-Y') }}
                </td>
            </tr>

            @foreach ($dayTypes as $type)
                @php
                    $rows = $results
                        ->where('over_money_type', $type->over_money_type)
                        ->where('over_money_date', $type->over_money_date)
                        ->values();
                    $subTotal = $rows->sum('over_amount');
                    $isUsd = $type->over_money_type === \App\Models\OverMoney::USD;
                @endphp
                <tr>
                    <td class="content_regular txt_left txt_bold" colspan="3">
                        {{ __('global.report_currency_type') }} : {{ $isUsd ? __('global.currency_usd') : __('global.currency_khr') }}
                    </td>
                </tr>
                <tr>
                    <td width="15%" class="khmer_moul_title_1">ល.រ</td>
                    <td width="60%" class="khmer_moul_title_1">{{ __('global.amount') }}</td>
                    <td width="25%" class="khmer_moul_title_1"></td>
                </tr>
                @foreach ($rows as $key => $item)
                    <tr>
                        <td class="content_regular">{{ $key + 1 }}</td>
                        <td class="content_regular">{{ $isUsd ? number_format($item->over_amount, 2) : number_format($item->over_amount) }}</td>
                        <td class="content_regular"></td>
                    </tr>
                @endforeach
                <tr>
                    <td class="content_regular txt_right txt_bold" style="font-size: 14pt;" colspan="2">
                        {{ __('global.total_sale') }} : {{ $isUsd ? number_format($subTotal, 2) : number_format($subTotal) }}
                    </td>
                    <td class="content_regular"></td>
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
