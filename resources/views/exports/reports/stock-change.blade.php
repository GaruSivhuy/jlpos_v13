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
            <td colspan="7">របាយការណ៍ស្តុក ដូររង្វាន់ជាឥវ៉ាន់<td>
        </tr>
        <tr valign="center">
            <td colspan="7">ចាប់ពីថ្ងៃទី{{ \App\Exports\Support\KhmerNumeral::digits($from->day) }} ខែ{{ \App\Exports\Support\KhmerNumeral::month($from->month) }} ឆ្នាំ{{ \App\Exports\Support\KhmerNumeral::digits($from->year) }} ដល់ថ្ងៃទី{{ \App\Exports\Support\KhmerNumeral::digits($to->day) }} ខែ{{ \App\Exports\Support\KhmerNumeral::month($to->month) }} ឆ្នាំ{{ \App\Exports\Support\KhmerNumeral::digits($to->year) }}<td>
        </tr>
        <tr valign="center">
            <td align="center">ល.រ</td>
            <td align="center">កាលបរិច្ឆេទដូរទំនិញ</td>
            <td align="center">ឈ្មោះទំនិញ</td>
            <td align="center">ថ្លៃដើម</td>
            <td align="center">ចំនួន</td>
            <td align="center">ថ្លៃដើមសរុប</td>
            <td align="center">ផ្សេងៗ</td>
        </tr>
        @foreach ($results as $key => $value)
            @php
                $productName = $value->name_kh.' - '.$value->pbar_code;
                $metricQty = optional(\App\Models\Metric::find($value->metric_id))->qty ?? 0;
                $changeQty = $metricQty > 0 ? $metricQty * $value->qty : 0;

                $cost = 0;
                $stock = \App\Stevebauman\Inventory\Models\InventoryStock::where('inventory_id', $value->inventory_id)->first();

                if ($stock) {
                    $movement = \App\Stevebauman\Inventory\Models\InventoryStockMovement::query()
                        ->selectRaw('SUM(CASE WHEN reason = "Stock In" THEN cost END) as total_cost')
                        ->selectRaw('SUM(CASE WHEN reason = "Stock In" THEN before END) as total_before')
                        ->selectRaw('SUM(CASE WHEN reason = "Stock In" THEN after END) as total_after')
                        ->where('stock_id', $stock->id)
                        ->groupBy('stock_id')
                        ->first();

                    if ($movement) {
                        $costQuantity = $movement->total_after - $movement->total_before;
                        $cost = $costQuantity != 0 ? round($movement->total_cost / $costQuantity, 2) : 0;
                    }
                }

                $totalCost = $cost * $changeQty;
            @endphp
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ \Illuminate\Support\Carbon::parse($value->change_product_date)->format('d-m-Y') }}</td>
                <td>{{ $productName }}</td>
                <td>{{ $cost }}</td>
                <td>{{ $changeQty }}</td>
                <td>{{ $totalCost }}</td>
                <td>&nbsp;</td>
            </tr>
        @endforeach
    </table>
</body>
</html>
