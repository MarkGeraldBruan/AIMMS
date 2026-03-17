<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RPCI - Report on the Physical Count of Inventories</title>
    <style>
        /* Embed DejaVu Sans so dompdf can render the ₱ glyph reliably. Place TTF at public/fonts/DejaVuSans.ttf */
        @font-face {
            font-family: 'DejaVu Sans';
            src: url('file://{{ str_replace('\\','/', public_path('fonts/DejaVuSans.ttf')) }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }
        @page {
            margin: 0.5cm;
            size: A4;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            margin: 0;
            padding: 0;
            font-size: 12px;
            line-height: 1.4;
            background: white !important;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
            page-break-after: avoid;
        }
        .header h1 { margin: 0; font-size: 14px; font-weight: bold; }
        .header p { margin: 3px 0; }
        .accountability {
            text-align: center;
            margin: 15px 0;
            padding: 10px;
            page-break-after: avoid;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            table-layout: fixed;
        }
        .table th, .table td {
            border: 1px solid #000;
            padding: 3px 2px;
            text-align: center;
            font-size: 8px;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        .table th {
            background-color: #f0f0f0;
            font-weight: bold;
            font-size: 8px;
        }

        /* Ensure all content fits within page */
        * {
            box-sizing: border-box !important;
        }

        /* Prevent content overflow */
        .table {
            max-width: 100% !important;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>REPORT ON THE PHYSICAL COUNT OF INVENTORIES</h1>
        <h1>COMMON SUPPLIES AND EQUIPMENTS</h1>
        <h1>(REGULAR)</h1>
        <p>As of {!! $header['as_of'] ?: '______' !!}</p>
        <p>Fund Cluster : {!! isset($header['fund_cluster']) && trim($header['fund_cluster']) !== '' ? e($header['fund_cluster']) : '______' !!}</p>
    </div>

    <div class="accountability">
        <p>
            For which
            {!! isset($header['accountable_person']) && trim($header['accountable_person']) !== '' ? e($header['accountable_person']) : '<span style="border-bottom:1px solid #000;padding:0 90px;display:inline-block;">&nbsp;</span>' !!},
            {!! isset($header['position']) && trim($header['position']) !== '' ? e($header['position']) : '<span style="border-bottom:1px solid #000;padding:0 90px;display:inline-block;">&nbsp;</span>' !!},
            {!! isset($header['office']) && trim($header['office']) !== '' ? e($header['office']) : '<span style="border-bottom:1px solid #000;padding:0 90px;display:inline-block;">&nbsp;</span>' !!}
            is accountable, having assumed such accountability on
            {!! isset($header['assumption_date']) && trim($header['assumption_date']) !== '' ? e(\Carbon\Carbon::parse($header['assumption_date'])->format('F d, Y')) : '<span style="border-bottom:1px solid #000;padding:0 90px;display:inline-block;">&nbsp;</span>' !!}.
        </p>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th rowspan="2">Article</th>
                <th rowspan="2">Description</th>
                <th rowspan="2">Stock Number</th>
                <th rowspan="2">Unit of Measure</th>
                <th rowspan="2">Unit Value</th>
                <th rowspan="2">Balance Per Card</th>
                <th rowspan="2">On Hand Per Count</th>
                <th colspan="2">Shortage/Overage</th>
                <th rowspan="2">Remarks</th>
            </tr>
            <tr>
                <th>Quantity</th>
                <th>Value</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rpciItems as $item)
                <tr>
                    <td>{{ $item->article }}</td>
                    <td>{{ $item->description }}</td>
                    <td>{{ $item->stock_number }}</td>
                    <td>{{ $item->unit_of_measure }}</td>
                    <td>{{ number_format($item->unit_value, 2) }}</td>
                    <td>{{ $item->balance_per_card }}</td>
                    <td>{{ $item->on_hand_per_count }}</td>
                    <td>{{ $item->shortage_overage_quantity }}</td>
                    <td>{{ number_format($item->shortage_overage_value, 2) }}</td>
                    <td>{{ $item->remarks }}</td>
                </tr>
            @empty
                <tr><td colspan="10">No records found.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
