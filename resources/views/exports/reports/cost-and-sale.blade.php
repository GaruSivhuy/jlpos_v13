<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body text="#000000" link="#000000" alink="#000000" vlink="#000000">
    @php
        $from = \Illuminate\Support\Carbon::parse($fromDate);
        $to = \Illuminate\Support\Carbon::parse($toDate);
    @endphp
    <table width="100%" style="margin-top: 10px;">
        <tr valign="center">
            <td colspan="10">របាយការណ៍តម្លៃគិតជាមធ្យម សម្រាប់ការទិញចូល និងលក់ចេញ<td>
        </tr>
        <tr valign="center">
            <td colspan="10">ចាប់ពីថ្ងៃទី{{ \App\Exports\Support\KhmerNumeral::digits($from->day) }} ខែ{{ \App\Exports\Support\KhmerNumeral::month($from->month) }} ឆ្នាំ{{ \App\Exports\Support\KhmerNumeral::digits($from->year) }} ដល់ថ្ងៃទី{{ \App\Exports\Support\KhmerNumeral::digits($to->day) }} ខែ{{ \App\Exports\Support\KhmerNumeral::month($to->month) }} ឆ្នាំ{{ \App\Exports\Support\KhmerNumeral::digits($to->year) }}<td>
        </tr>
        <tr valign="center">
            <td align="center">ល.រ</td>
            <td align="center">ឈ្មោះទំនិញ</td>
            <td align="center">ចំនួនទិញចូល</td>
            <td align="center">តម្លៃទិញចូលសរុប</td>
            <td align="center">តម្លៃទិញចូល (គិតជាធម្យម)</td>
            <td align="center">ចំនួនលក់ចេញ</td>
            <td align="center">ចំនួនលក់ចេញសរុប</td>
            <td align="center">តម្លៃលក់ចេញ (គិតជាមធ្យម)</td>
            <td align="center">ផលចំនេញ</td>
            <td align="center">ផ្សេងៗ</td>
        </tr>
        @php $rowNumber = 0; @endphp
        @foreach ($results as $value)
            @php
                $totalSaleQty = $value->total_sale_qty ?: 0;
            @endphp
            @continue($totalSaleQty == 0)
            @php
                $rowNumber++;
                $productName = $value->name_kh.' - '.$value->pbar_code;
                $quantity = $value->total_after - $value->total_before;
                $cost = $quantity != 0 ? round($value->total_cost / $quantity, 2) : 0;
                $totalSaleAmount = $value->total_sale_amount ?: 0;
                $salePrice = $totalSaleAmount != 0 ? round($totalSaleAmount / $totalSaleQty, 2) : 0;
            @endphp
            <tr>
                <td>{{ $rowNumber }}</td>
                <td>{{ $productName }}</td>
                <td>{{ $quantity }}</td>
                <td>{{ $value->total_cost }}</td>
                <td>{{ $cost }}</td>
                <td>{{ $totalSaleQty }}</td>
                <td>{{ $totalSaleAmount }}</td>
                <td>{{ $salePrice }}</td>
                <td>{{ ($salePrice - $cost) * $totalSaleQty }}</td>
                <td>&nbsp;</td>
            </tr>
        @endforeach
    </table>
</body>
</html>
