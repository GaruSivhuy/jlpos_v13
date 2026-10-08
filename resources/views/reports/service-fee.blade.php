<!DOCTYPE html>
<html lang="km">
@include('reports.partials.print-head', ['title' => __('global.report_service_fee')])
<body>
    <div class="close_btn" style="text-align: center; margin-top: 10px;">
        <a href="{{ \App\Filament\Pages\Reports::getUrl() }}" class="button-link">{{ __('global.close') }}</a>
    </div>

    @include('reports.partials.print-header', ['title' => __('global.report_service_fee')])

    <table class="table_1">
        @forelse ($results->groupBy(fn ($item) => $item->created_at->format('d-m-Y')) as $date => $dayRows)
            <tr>
                <td class="content_regular txt_bold" colspan="4">
                    {{ __('global.date') }} : {{ $date }}
                </td>
            </tr>

            @foreach ($dayRows->groupBy('service_type') as $serviceType => $rows)
                @php
                    $isUsd = (int) $serviceType === \App\Models\ServiceFee::TYPE_USD;
                    $typeLabel = $isUsd ? __('global.service_type_usd') : __('global.service_type_riel');
                    $subTotalAmount = $rows->sum('amount');
                    $subTotalFees = $rows->sum('service_fees');
                @endphp
                <tr>
                    <td class="content_regular txt_left txt_bold" colspan="4">
                        {{ __('global.service_type') }} : {{ $typeLabel }}
                    </td>
                </tr>
                <tr>
                    <td width="10%" class="khmer_moul_title_1">ល.រ</td>
                    <td width="30%" class="khmer_moul_title_1">{{ __('global.service_type') }}</td>
                    <td width="30%" class="khmer_moul_title_1">{{ __('global.amount') }}</td>
                    <td width="30%" class="khmer_moul_title_1">{{ __('global.service_fees') }}</td>
                </tr>
                @foreach ($rows->values() as $key => $item)
                    <tr>
                        <td class="content_regular">{{ $key + 1 }}</td>
                        <td class="content_regular">{{ $typeLabel }}</td>
                        <td class="content_regular">
                            {{ $isUsd ? number_format($item->amount, 2).' $' : number_format($item->amount).' ៛' }}
                        </td>
                        <td class="content_regular">{{ number_format($item->service_fees) }} ៛</td>
                    </tr>
                @endforeach
                <tr>
                    <td class="content_regular txt_right txt_bold" colspan="2">{{ __('global.total_amount') }}</td>
                    <td class="content_regular txt_bold">
                        {{ $isUsd ? number_format($subTotalAmount, 2).' $' : number_format($subTotalAmount).' ៛' }}
                    </td>
                    <td class="content_regular txt_bold">{{ number_format($subTotalFees) }} ៛</td>
                </tr>
            @endforeach
        @empty
            <tr>
                <td class="content_regular">{{ __('global.report_no_data') }}</td>
            </tr>
        @endforelse
    </table>

    @include('reports.partials.print-script')
</body>
</html>
