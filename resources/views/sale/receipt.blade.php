<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="utf-8" />
    <title>{{ $invoice->invoice_code }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Battambang:wght@400;700&family=Moul&display=swap" rel="stylesheet" />
    <style>
        @page {
            size: auto;
            margin: 5mm;
        }

        @media print {
            * { -webkit-print-color-adjust: exact; }
        }

        a { text-decoration: none; }

        table {
            border-collapse: collapse;
            margin-top: 2px;
        }

        tr {
            page-break-inside: avoid !important;
            page-break-after: auto !important;
        }

        .title_kh {
            font-size: 12px !important;
            color: #000000;
            line-height: 1.8393555;
            font-family: 'Moul', 'Times New Roman' !important;
        }

        .battambang {
            font-family: 'Battambang', 'Times New Roman' !important;
            font-size: 9px !important;
            color: #000000;
            line-height: 1.7393555;
        }

        .battambangbold {
            font-family: 'Battambang', 'Times New Roman' !important;
            font-size: 7pt !important;
            color: #000000;
            font-weight: bold;
            line-height: 1.8393555;
        }

        .txt_center { text-align: center; }
        .txt_right { text-align: right; }
        .padding { padding: 3px; height: 20px; }
        .padding-1 { padding-left: 5px; height: 20px; }
        .border-right { border-right: 1px solid black; }
    </style>
</head>
@php
    $sign = env('FOREIGN_CURRENCY_SIGN', '$');
    $localSign = env('LOCAL_CURRENCY', '៛');
    $exchangeRate = (float) $invoice->exchange?->exchange_rate;
    $subTotal = 0;
@endphp
<body text="#000000" link="#000000" alink="#000000" vlink="#000000">
    <table width="100%">
        <tr valign="center">
            <td align="left">
                <img src="{{ url('backend/images/JL.png') }}" width="60px" style="vertical-align: middle;" alt="" />
            </td>
            <td align="right" style="padding-left: 5px;">
                <span class="title_kh" style="font-size: 9pt !important;">{{ $company }}</span><br>
                <span class="battambang" style="font-size: 6pt !important;">{{ $address }}</span><br>
                <span class="battambang" style="font-weight: bold; font-size: 8pt !important;">096 2 8888 45 <br> 099 947 272 <br> 071 698 8826</span>
            </td>
        </tr>
    </table>
    <hr />
    <div class="txt_center title_kh">វិក្កយបត្រ</div>
    <table width="100%" style="position: absolute; margin-top: -30px;">
        <tr>
            <td style="font-size: 20pt; text-align: right; padding-right: 35px;">{{ $invoice->order_number }}</td>
        </tr>
    </table>
    <table width="100%">
        <tr valign="center">
            <td class="battambang" width="30%">អ្នកលក់ :</td>
            <td class="battambang" width="20%">{{ $invoice->createdBy?->name ?? auth()->user()->name }}</td>
            <td class="battambang" width="30%" style="text-align: right; padding-right: 20px;">អតិថិជន :</td>
            <td class="battambang" width="20%">{{ $invoice->customer?->name_kh ?? 'អតិថិជនទូទៅ' }}</td>
        </tr>
        <tr>
            <td class="battambang">កាលបរិច្ឆេទ :</td>
            <td class="battambang">{{ $invoice->invoiced_at?->format('Y-m-d H:i:s') }}</td>
            <td class="battambang" style="text-align: right; padding-right: 20px;">ប្រភេទបង់ប្រាក់ :</td>
            <td class="battambang">{{ $invoice->paymentGateway?->name ?? 'N/A' }}</td>
        </tr>
        <tr valign="center">
            <td class="battambang">លេខវិក្កយបត្រ :</td>
            <td class="battambang">{{ $invoice->invoice_code }}</td>
            <td class="battambang" style="text-align: right; padding-right: 20px;">អត្រាប្តូរប្រាក់ :</td>
            <td class="battambang">{{ number_format($exchangeRate) }}</td>
        </tr>
    </table>
    <table width="100%">
        <tr valign="center" style="background-color: lightgray; border: 1px solid black !important;">
            <td class="battambangbold txt_center padding border-right" width="8%">ល.រ</td>
            <td class="battambangbold txt_center padding border-right" width="32%">ឈ្មោះទំនិញ</td>
            <td class="battambangbold txt_center padding border-right" width="15%">ចំនួន</td>
            <td class="battambangbold txt_center padding border-right" width="15%">តម្លៃ</td>
            <td class="battambangbold txt_center padding border-right" width="15%">ចុះតម្លៃ</td>
            <td class="battambangbold txt_center padding" width="15%">សរុប</td>
        </tr>
        @foreach ($invoice->invoiceItems as $item)
            @php
                $subTotal += $item->amount;
                $discount = $item->discount_type === 'F' ? $item->discount : $item->amount * ($item->discount / 100);
                $metricShow = $item->metric?->name_show ?? '';
                $remarkNames = $item->product?->remarks->pluck('name_kh') ?? collect();
            @endphp
            <tr valign="center" style="border: 1px solid black !important;">
                <td class="battambang txt_center padding border-right">{{ $loop->iteration }}</td>
                <td class="battambang padding-1 border-right">
                    {{ $item->product?->name_kh ?? 'Undefined' }}<br>
                    @if ($remarkNames->isNotEmpty())
                        [ {{ $remarkNames->implode('............. ') }}............. ]
                    @endif
                </td>
                <td class="battambang txt_center padding border-right">
                    @if ($metricShow === 'កន្លះកេស'){{ $metricShow }}@else{{ $item->qty.' '.$metricShow }}@endif
                </td>
                <td class="battambang txt_center padding border-right">{{ number_format($item->price, 2) }} {{ $sign }}</td>
                <td class="battambang txt_center padding border-right">{{ number_format($discount, 2) }} {{ $sign }}</td>
                <td class="battambang txt_center padding">{{ number_format($item->amount, 2) }} {{ $sign }}</td>
            </tr>
        @endforeach
    </table>
    <table style="width: 100%; margin-top: 10px;">
        <tr valign="middle">
            <td rowspan="4" width="45%" class="battambang" style="font-size: 5pt !important; vertical-align: top;">*សូមពិនិត្យទំនិញមុន និងរាប់ចំនួនអោយបានត្រឹមត្រូវមុននឹងចាកចេញ<br>*ទំនិញដែលទិញរួចហើយ មិនអាចប្តូរ ឬដកលុយវិញបានទេ<br>*យើងខ្ញុំសូមអរគុណសម្រាប់ការគាំទ្ររបស់លោកអ្នក</td>
            <td class="battambangbold txt_right">ដុល្លារ :</td>
            <td class="battambangbold txt_right" style="font-size: 8pt !important;">{{ number_format($subTotal, 2) }} {{ $sign }}&nbsp;&nbsp;</td>
        </tr>
        <tr valign="middle">
            <td class="battambangbold txt_right">បញ្ចុះតម្លៃ :</td>
            <td class="battambangbold txt_right">{{ number_format($invoice->discount, 2) }} {{ $sign }}&nbsp;&nbsp;</td>
        </tr>
        <tr valign="middle">
            <td class="battambangbold txt_right">លុយសរុបដុល្លារ :</td>
            <td class="battambangbold txt_right" style="font-size: 8pt !important;">{{ number_format($invoice->total, 2) }} {{ $sign }}&nbsp;&nbsp;</td>
        </tr>
        <tr valign="middle">
            <td class="battambangbold txt_right">លុយសរុបរៀល :</td>
            <td class="battambangbold txt_right">{{ number_format($invoice->total * $exchangeRate) }} ៛&nbsp;&nbsp;</td>
        </tr>
    </table>
    <hr>
    <div class="txt_center">
        <span class="battambangbold">លុយទទួលដុល្លារ : {{ number_format($invoice->paid_amount_usd, 2) }} {{ $sign }}&nbsp;&nbsp;@if ($invoice->paid_amount_riel != 0)/&nbsp;&nbsp;រៀល :&nbsp;&nbsp;{{ number_format($invoice->paid_amount_riel, 2) }} {{ $localSign }}&nbsp;&nbsp;@endif</span><br>
        <span class="battambangbold">លុយអាប់ដុល្លារ : {{ number_format($invoice->total_return, 2) }} {{ $sign }}&nbsp;&nbsp;@if ($invoice->total_return_riel != 0)/&nbsp;&nbsp;រៀល :&nbsp;&nbsp;{{ number_format($invoice->total_return_riel, 2) }} {{ $localSign }}&nbsp;&nbsp;@endif</span>
    </div>
    <script>
        window.addEventListener('load', function () {
            window.print();
            setTimeout(function () { window.close(); }, 0);
        });
    </script>
</body>
</html>
