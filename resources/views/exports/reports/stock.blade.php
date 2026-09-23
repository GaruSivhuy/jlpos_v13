<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body text="#000000" link="#000000" alink="#000000" vlink="#000000">
    <table width="100%" style="margin-top: 10px;">
        <tr valign="center">
            <td colspan="7">របាយការណ៍ស្តុកទាំងអស់<td>
        </tr>
        <tr valign="center">
            <td align="center">ល.រ</td>
            <td align="center">ឈ្មោះទំនិញ</td>
            <td align="center">ថ្លៃដើម</td>
            <td align="center">ចំនួន</td>
            <td align="center">ខ្នាត</td>
            <td align="center">ថ្លៃដើមសរុប</td>
            <td align="center">ផ្សេងៗ</td>
        </tr>
        @foreach ($results as $key => $value)
            @php
                $productName = $value->name_kh.' - '.$value->pbar_code;
                $metric = \App\Models\Metric::query()
                    ->join('metricsables', 'metrics.id', '=', 'metricsables.metric_id')
                    ->where('metricsables.metricsables_id', $value->inventory_id)
                    ->orderBy('metrics.qty')
                    ->first();
                $metricShow = $metric ? $metric->name_show.' ('.$metric->qty.') ' : '';
                $quantity = $value->total_after - $value->total_before;
                $cost = $quantity != 0 ? round($value->total_cost / $quantity, 2) : 0;
                $totalCost = $value->quantity ? round($cost * $value->quantity, 2) : 0;
            @endphp
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ $productName }}</td>
                <td>{{ $cost }}</td>
                <td>{{ $value->quantity }}</td>
                <td>{{ $metricShow }}</td>
                <td>{{ $totalCost }}</td>
                <td>&nbsp;</td>
            </tr>
        @endforeach
    </table>
</body>
</html>
