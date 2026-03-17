<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RSMI - Report of Supplies and Materials Issued</title>
    <style>
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
        .recap-container { width: 100%; margin-top: 15px; }
        .recap-table { width: 100%; border-collapse: collapse; }
        .recap-table td { vertical-align: top; padding: 0 8px; }
        .recap-table td:first-child { width: 50%; }
        .recap { width: 100%; }
        .recap table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
            table-layout: fixed;
        }
        .recap th, .recap td {
            border: 1px solid #000;
            padding: 3px 2px;
            text-align: center;
            font-size: 7px;
            height: 18px;
            word-wrap: break-word;
        }
        .recap th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .recap p { margin: 3px 0; font-weight: bold; font-size: 9px; }

        /* Ensure all content fits within page */
        * {
            box-sizing: border-box !important;
        }

        /* Prevent content overflow */
        .table, .recap table {
            max-width: 100% !important;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Republic of the Philippines</h1>
        <h1>{!! isset($header['entity_name']) && trim($header['entity_name']) !== '' ? e($header['entity_name']) : '______' !!}</h1>
        <h1>Report of Supplies and Materials Issued</h1>
        <p>For the Month of {!! $header['as_of'] ?: now()->format('F Y') !!}</p>
        @if(!empty($header['applied_filters']))
            <p>{!! $header['applied_filters'] !!}</p>
        @endif
        <p>Fund Cluster: {!! isset($header['fund_cluster']) && trim($header['fund_cluster']) !== '' ? e($header['fund_cluster']) : '__________________________' !!}</p>
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
                <th>RIS No.</th>
                <th>Responsibility Center Code</th>
                <th>Stock No.</th>
                <th>Item</th>
                <th>Unit</th>
                <th>Quantity Issued</th>
                <th>Unit Cost</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rsmiItems as $item)
                <tr>
                    <td>{{ $item->issue_no }}</td>
                    <td>{{ $item->responsibility_center }}</td>
                    <td>{{ $item->stock_no }}</td>
                    <td>{{ $item->item }}</td>
                    <td>{{ $item->unit }}</td>
                    <td>{{ $item->quantity_issued }}</td>
                    <td>{{ number_format($item->unit_cost, 2) }}</td>
                    <td>{{ number_format($item->amount, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="8">No records found.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="recap-container">
        <table class="recap-table">
            <tr>
                <td>
                    <div class="recap">
                        <p><strong>Recapitulation:</strong></p>
                        <table>
                            <thead>
                                <tr>
                                    <th>Stock No.</th>
                                    <th>Quantity</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recapLeft as $item)
                                    <tr>
                                        <td>{{ $item['stock_no'] }}</td>
                                        <td>{{ $item['quantity'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </td>
                <td>
                    <div class="recap">
                        <p><strong>Recapitulation:</strong></p>
                        <table>
                            <thead>
                                <tr>
                                    <th>Unit Cost</th>
                                    <th>Total Cost</th>
                                    <th>UACS Object Code</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ number_format($recapRight['unit_cost'], 2) }}</td>
                                    <td>{{ number_format($recapRight['total_cost'], 2) }}</td>
                                    <td>{{ $recapRight['uacs_code'] }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
