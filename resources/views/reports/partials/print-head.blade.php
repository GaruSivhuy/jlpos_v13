<head>
    <meta charset="utf-8" />
    <title>{{ $title }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Battambang:wght@400;700&family=Moul&display=swap" rel="stylesheet" />
    <style>
        @page {
            size: A4;
            margin: 5mm;
        }

        @media print {
            * { -webkit-print-color-adjust: exact; }
            .close_btn { display: none; }
        }

        body {
            max-width: 1024px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        tr {
            page-break-inside: avoid !important;
        }

        td {
            padding: 5px;
            text-align: center;
        }

        .khmer_moul_title {
            font-size: 12pt;
            font-family: 'Moul', 'Times New Roman';
            line-height: 1.8;
        }

        .khmer_moul_title_1 {
            font-size: 10pt;
            font-family: 'Moul', 'Times New Roman';
        }

        .content_regular {
            font-size: 10pt;
            font-family: 'Battambang', 'Times New Roman';
            line-height: 1.5;
        }

        .table_1 {
            border: solid #000;
            border-width: 1px 0 0 1px;
        }

        .table_1 td {
            border: solid #000;
            border-width: 0 1px 1px 0;
        }

        .txt_left { text-align: left; }
        .txt_right { text-align: right; }
        .txt_bold { font-weight: bold; }
        .txt_danger { color: #b91c1c; }

        .button-link {
            padding: 10px 15px;
            background: #4479ba;
            color: #fff;
            border-radius: 4px;
            border: solid 1px #20538d;
            text-decoration: none;
        }
    </style>
</head>
