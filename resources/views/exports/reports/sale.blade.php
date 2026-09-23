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
            <td colspan="8">របាយការណ៍លក់<td>
        </tr>
        <tr valign="center">
            <td colspan="8">ចាប់ពីថ្ងៃទី{{ \App\Exports\Support\KhmerNumeral::digits($from->day) }} ខែ{{ \App\Exports\Support\KhmerNumeral::month($from->month) }} ឆ្នាំ{{ \App\Exports\Support\KhmerNumeral::digits($from->year) }} ដល់ថ្ងៃទី{{ \App\Exports\Support\KhmerNumeral::digits($to->day) }} ខែ{{ \App\Exports\Support\KhmerNumeral::month($to->month) }} ឆ្នាំ{{ \App\Exports\Support\KhmerNumeral::digits($to->year) }}<td>
        </tr>
        <tr valign="center">
            <td align="center">ល.រ</td>
            <td align="center">កាលបរិច្ឆេទលក់ទំនិញ</td>
            <td align="center">ឈ្មោះទំនិញ</td>
            <td align="center">ចំនួន</td>
            <td align="center">តម្លៃ</td>
            <td align="center">បញ្ចុះតម្លៃ</td>
            <td align="center">សរុប</td>
            <td align="center">ផ្សេងៗ</td>
        </tr>
        @foreach ($results as $key => $value)
            @php
                $productName = $value->name_kh.' - '.$value->pbar_code;
                $discount = $value->discount_type === 'F' ? $value->discount : ($value->amount * ($value->discount / 100));
            @endphp
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ \Illuminate\Support\Carbon::parse($value->payment_date)->format('d-m-Y') }}</td>
                <td>{{ $productName }}</td>
                <td>{{ $value->qty }}</td>
                <td>{{ round($value->price, 2) }}</td>
                <td>{{ $discount }}</td>
                <td>{{ round($value->amount, 2) }}</td>
                <td>&nbsp;</td>
            </tr>
        @endforeach
    </table>
</body>
</html>
