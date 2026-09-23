<!DOCTYPE html>
<html lang="km">
@include('reports.partials.print-head', ['title' => __('global.report_exchange_product')])
<body>
    <div class="close_btn" style="text-align: center; margin-top: 10px;">
        <a href="{{ \App\Filament\Pages\Reports::getUrl() }}" class="button-link">{{ __('global.close') }}</a>
    </div>

    @include('reports.partials.print-header', ['title' => __('global.report_exchange_product')])

    @php
        use App\Models\ChangeProduct;

        $grandTotal = 0;

        $typeLabels = [
            ChangeProduct::TYPE_ITEM => __('global.change_product_type_item'),
            ChangeProduct::TYPE_CASH => __('global.change_product_type_cash'),
            ChangeProduct::TYPE_EARRING => __('global.change_product_type_earring'),
        ];
    @endphp

    <table class="table_1">
        @forelse ($resultsGroupBy as $group)
            @php
                $dayRows = $results->where('change_product_date', $group->change_product_date)->values();
                $dayTypes = $resultsType->where('change_product_date', $group->change_product_date)->values();
                $dayTotal = $dayRows->sum('total_amount');
                $grandTotal += $dayTotal;
            @endphp
            <tr>
                <td class="content_regular txt_bold" colspan="6">
                    {{ __('global.date') }} : {{ \Illuminate\Support\Carbon::parse($group->change_product_date)->format('d-m-Y') }}
                </td>
            </tr>

            @foreach ($dayTypes as $type)
                @php
                    $rows = $dayRows->where('change_product_type', $type->change_product_type)->values();
                    $subTotal = $rows->sum('total_amount');
                @endphp
                <tr>
                    <td class="content_regular txt_left txt_bold" colspan="6">
                        {{ __('global.change_product_type') }} : {{ $typeLabels[$type->change_product_type] ?? '' }}
                    </td>
                </tr>
                <tr>
                    <td class="khmer_moul_title_1">ល.រ</td>
                    <td width="30%" class="khmer_moul_title_1">{{ __('global.change_description') }}</td>
                    <td class="khmer_moul_title_1">{{ __('global.sale_qty') }}</td>
                    <td class="khmer_moul_title_1">{{ __('global.amount') }}</td>
                    <td class="khmer_moul_title_1">{{ __('global.total_amount') }}</td>
                    <td class="khmer_moul_title_1"></td>
                </tr>
                @foreach ($rows as $key => $item)
                    @php $metricShow = $item->metrics?->name_show; @endphp
                    <tr>
                        <td width="5%" class="content_regular">{{ $key + 1 }}</td>
                        <td width="30%" class="content_regular txt_left">{{ $item->inventories?->name_kh ?? $item->change_description }}</td>
                        <td class="content_regular">{{ $metricShow === 'កន្លះកេស' ? $metricShow : trim($item->qty.' '.$metricShow) }}</td>
                        <td class="content_regular">{{ $item->amount ? number_format($item->amount) : '' }}</td>
                        <td class="content_regular">{{ $item->total_amount ? number_format($item->total_amount) : '' }}</td>
                        <td class="content_regular"></td>
                    </tr>
                @endforeach
                <tr>
                    <td class="content_regular txt_bold" colspan="3">{{ __('global.report_subtotal_by_type') }}</td>
                    <td class="content_regular" colspan="2">{{ number_format($subTotal) }}</td>
                    <td class="content_regular"></td>
                </tr>
            @endforeach
            <tr>
                <td class="khmer_moul_title_1" colspan="3">{{ __('global.total_sale') }}</td>
                <td class="content_regular txt_bold" colspan="2">{{ number_format($dayTotal) }}</td>
                <td class="content_regular"></td>
            </tr>
        @empty
            <tr>
                <td class="content_regular">{{ __('global.report_no_data') }}</td>
            </tr>
        @endforelse

        @if ($resultsGroupBy->isNotEmpty())
            <tr>
                <td class="khmer_moul_title_1" colspan="3">{{ __('global.total_sale') }}</td>
                <td class="content_regular txt_bold" colspan="2">{{ number_format($grandTotal) }}</td>
                <td class="content_regular"></td>
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
