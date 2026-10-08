<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="utf-8" />
    <title>{{ __('global.service_fee_receipt') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}" />
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

        .txt_center { text-align: center; }
        .txt_right { text-align: right; padding-right: 20px; }

        .button-link {
            padding: 10px 15px;
            background: #4479ba;
            color: #fff;
            border-radius: 4px;
            border: solid 1px #20538d;
        }
    </style>
</head>
<body text="#000000" link="#000000" alink="#000000" vlink="#000000">
    <div class="close_btn" style="text-align: center; margin-top: 20px;">
        <a href="{{ $closeUrl }}" class="button-link">{{ __('global.close') }}</a>
    </div>
    <div id="section-to-print">
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
        <div class="txt_center title_kh">{{ __('global.service_fee_receipt') }}</div>
        <table width="100%" style="position: absolute; margin-top: -30px;">
            <tr>
                <td style="font-size: 20pt; text-align: right; padding-right: 35px;">{{ $serviceFee->id }}</td>
            </tr>
        </table>
        <table width="100%">
            <tr valign="center">
                <td class="battambang" width="30%">{{ __('global.seller') }} :</td>
                <td class="battambang" width="20%">{{ $serviceFee->user_create?->name ?? auth()->user()->name }}</td>
                <td class="battambang txt_right" width="30%">{{ __('global.date') }} :</td>
                <td class="battambang" width="20%">{{ ($serviceFee->created_at ?? today())->format('d-m-Y') }}</td>
            </tr>
        </table>
        <hr>
        <table width="100%">
            <tr>
                <td class="battambang" width="50%">&nbsp;</td>
                <td class="battambang txt_right" width="50%">{{ __('global.service_type') }} = {{ $serviceType }}</td>
            </tr>
            <tr valign="center">
                <td class="battambang">&nbsp;</td>
                <td class="battambang txt_right">{{ __('global.amount') }} = {{ number_format($serviceFee->amount, 2) }}</td>
            </tr>
            <tr valign="center">
                <td class="battambang">&nbsp;</td>
                <td class="battambang txt_right">{{ __('global.service_fees') }} = {{ number_format($serviceFee->service_fees, 2) }}</td>
            </tr>
        </table>
        <hr>
    </div>
    <script>
        window.addEventListener('load', function () {
            window.print();
        });

        // Close the receipt once the print dialog is done, a tab the browser refuses to close goes back to the list.
        window.addEventListener('afterprint', function () {
            window.close();

            setTimeout(function () {
                window.location.href = @js($closeUrl);
            }, 300);
        });
    </script>
</body>
</html>
