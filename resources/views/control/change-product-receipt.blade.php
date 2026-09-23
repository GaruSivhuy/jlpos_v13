<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="utf-8" />
    <title>{{ __('global.change_product') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Battambang:wght@400;700&family=Moul&display=swap" rel="stylesheet" />
    <style>
        @page {
            size: auto;
            margin: 5mm;
        }

        @media print {
            * { -webkit-print-color-adjust: exact; }
            .close_btn { display: none; }
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
            font-size: 12pt !important;
            color: #000000;
            line-height: 1.8393555;
            font-family: 'Moul', 'Times New Roman' !important;
        }

        .battambang {
            font-family: 'Battambang', 'Times New Roman' !important;
            font-size: 11pt !important;
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
        .padding { padding: 3px; height: 20px; }
        .padding-1 { padding-left: 5px; height: 20px; }
        .border-right { border-right: 1px solid black; }

        .button-link {
            padding: 10px 15px;
            background: #4479ba;
            color: #fff;
            border-radius: 4px;
            border: solid 1px #20538d;
        }
    </style>
</head>
@php
    $total = $results->sum('total_amount');
@endphp
<body text="#000000" link="#000000" alink="#000000" vlink="#000000">
    <div class="close_btn" style="text-align: center; margin-top: 20px;">
        <a href="{{ $closeUrl }}" class="button-link">{{ __('global.close') }}</a>
    </div>
    <table width="100%">
        <tr valign="center">
            <td align="left">
                <img src="{{ url('backend/images/JL.png') }}" width="60px" style="vertical-align: middle;" alt="" />
            </td>
            <td align="right" style="padding-left: 5px;">
                <span class="title_kh" style="font-size: 9pt !important;">{{ $company }}</span><br>
                <span class="battambang" style="font-size: 6pt !important;">{{ $address }}</span><br>
                <span class="battambang" style="font-weight: bold; font-size: 8pt !important;">{{ $phone }}</span>
            </td>
        </tr>
    </table>
    <hr />
    <div class="txt_center title_kh" style="font-size: 12pt !important;">{{ __('global.change_product') }}</div>
    <table width="100%" style="position: absolute; margin-top: -30px;">
        <tr>
            <td style="font-size: 14pt; text-align: right; padding-right: 20px;">{{ $showNumber }}</td>
        </tr>
    </table>
    <table width="100%">
        <tr valign="center">
            <td class="battambang" width="30%">{{ __('global.seller') }} :</td>
            <td class="battambang" width="20%">{{ auth()->user()->name }}</td>
            <td class="battambang" width="30%" style="text-align: right; padding-right: 20px;">{{ __('global.customer') }} :</td>
            <td class="battambang" width="20%">អតិថិជនទូទៅ</td>
        </tr>
        <tr>
            <td class="battambang">{{ __('global.date') }} :</td>
            <td class="battambang">{{ now()->format('d-m-Y') }}</td>
        </tr>
    </table>
    <table width="100%">
        <tr valign="center" style="background-color: lightgray; border: 1px solid black !important;">
            <td class="battambangbold txt_center padding border-right" width="8%">ល.រ</td>
            <td class="battambangbold txt_center padding border-right" width="42%">ឈ្មោះទំនិញ</td>
            <td class="battambangbold txt_center padding border-right" width="15%">{{ __('global.qty') }}</td>
            <td class="battambangbold txt_center padding border-right" width="15%">{{ __('global.amount') }}</td>
            <td class="battambangbold txt_center padding" width="20%">{{ __('global.total_amount') }}</td>
        </tr>
        @foreach ($results as $key => $item)
            @php
                $metricShow = $item->metrics?->name_show;
            @endphp
            <tr valign="center" style="border: 1px solid black !important;">
                <td class="battambang txt_center padding border-right">{{ $key + 1 }}</td>
                <td class="battambang padding-1 border-right">{{ $item->inventories?->name_kh ?? $item->change_description }}</td>
                <td class="battambang txt_center padding border-right">{{ $metricShow === 'កន្លះកេស' ? $metricShow : trim($item->qty.' '.$metricShow) }}</td>
                <td class="battambang txt_center padding border-right">{{ number_format($item->amount) }} ៛</td>
                <td class="battambang txt_center padding">{{ number_format($item->total_amount) }} ៛</td>
            </tr>
        @endforeach
        <tr valign="center" style="border: 1px solid black !important;">
            <td colspan="4" class="battambang padding border-right" style="text-align: right; font-size: 10pt !important;">{{ __('global.total_amount') }} :</td>
            <td class="battambangbold padding txt_center border-right" style="font-size: 10pt !important;">{{ number_format($total) }} ៛</td>
        </tr>
    </table>
    <script>
        window.addEventListener('load', function () {
            window.print();
        });
    </script>
</body>
</html>
