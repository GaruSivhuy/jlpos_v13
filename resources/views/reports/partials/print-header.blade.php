<table>
    <tr>
        <td class="khmer_moul_title">
            {{ $title }}
            <br>
            {{ __('global.from_date') }} :
            <span class="txt_bold" style="font-family: Arial;">{{ \Illuminate\Support\Carbon::parse($fromDate)->format('d-m-Y') }}</span>
            &nbsp;—&nbsp;
            {{ __('global.to_date') }} :
            <span class="txt_bold" style="font-family: Arial;">{{ \Illuminate\Support\Carbon::parse($toDate)->format('d-m-Y') }}</span>
            <br>
            @if ($cashier)
                {{ __('global.seller') }} : {{ $cashier }}
            @else
                {{ __('global.report_cashier_all') }}
            @endif
        </td>
    </tr>
</table>
