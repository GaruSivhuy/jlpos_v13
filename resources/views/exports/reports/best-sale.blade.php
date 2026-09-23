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
            <td colspan="4">របាយការណ៍ចំណាត់ថ្នាក់ការលក់ផលិតផល <br>តាមប្រភេទ<td>
        </tr>
        <tr valign="center">
            <td colspan="4">ចាប់ពីថ្ងៃទី{{ \App\Exports\Support\KhmerNumeral::digits($from->day) }} ខែ{{ \App\Exports\Support\KhmerNumeral::month($from->month) }} ឆ្នាំ{{ \App\Exports\Support\KhmerNumeral::digits($from->year) }} ដល់ថ្ងៃទី{{ \App\Exports\Support\KhmerNumeral::digits($to->day) }} ខែ{{ \App\Exports\Support\KhmerNumeral::month($to->month) }} ឆ្នាំ{{ \App\Exports\Support\KhmerNumeral::digits($to->year) }}<td>
        </tr>
        <tr valign="center">
            <td align="center">ល.រ</td>
            <td align="center">ឈ្មោះទំនិញ</td>
            <td align="center">ចំនួន</td>
            <td align="center">ផ្សេងៗ</td>
        </tr>
        @foreach ($results as $key => $value)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ $value->name_kh.' - '.$value->pbar_code }}</td>
                <td>{{ $value->total_qty }}</td>
                <td>&nbsp;</td>
            </tr>
        @endforeach
    </table>
</body>
</html>
