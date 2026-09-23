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
            <td colspan="8">របាយការណ៍ទិញទំនិញចូលស្តុក<td>
        </tr>
        <tr valign="center">
            <td colspan="8">ចាប់ពីថ្ងៃទី{{ \App\Exports\Support\KhmerNumeral::digits($from->day) }} ខែ{{ \App\Exports\Support\KhmerNumeral::month($from->month) }} ឆ្នាំ{{ \App\Exports\Support\KhmerNumeral::digits($from->year) }} ដល់ថ្ងៃទី{{ \App\Exports\Support\KhmerNumeral::digits($to->day) }} ខែ{{ \App\Exports\Support\KhmerNumeral::month($to->month) }} ឆ្នាំ{{ \App\Exports\Support\KhmerNumeral::digits($to->year) }}<td>
        </tr>
        <tr valign="center">
            <td align="center">ល.រ</td>
            <td align="center">កាលបរិច្ឆេទបញ្ជាទិញ</td>
            <td align="center">ឈ្មោះទំនិញ</td>
            <td align="center">ចំនួន</td>
            <td align="center">តម្លៃទិញចូល</td>
            <td align="center">សរុបតម្លៃទិញចូល</td>
            <td align="center">តម្លៃលក់ចេញ</td>
            <td align="center">ផ្សេងៗ</td>
        </tr>
        @foreach ($results as $key => $value)
            @php
                $productName = $value->name_kh.' - '.$value->pbar_code;
                $quantity = $value->after - $value->before;
                $price = $quantity != 0 ? round($value->cost / $quantity, 2) : 0;
                $salePrice = \App\Models\Metricsables::where('metricsables_id', $value->pid)->min('price') ?? 0;
            @endphp
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ \Illuminate\Support\Carbon::parse($value->created_at)->format('d-m-Y') }}</td>
                <td>{{ $productName }}</td>
                <td>{{ $quantity }}</td>
                <td>{{ $price }}</td>
                <td>{{ $value->cost }}</td>
                <td>{{ $salePrice }}</td>
                <td>&nbsp;</td>
            </tr>
        @endforeach
    </table>
</body>
</html>
