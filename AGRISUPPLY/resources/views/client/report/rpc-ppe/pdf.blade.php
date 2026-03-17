<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RPC-PPE Report - PDF</title>
    <style>
        /* Ensure dompdf can load a Unicode font that contains the ₱ glyph.
           Place DejaVuSans.ttf in public/fonts/DejaVuSans.ttf */
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
            /* Use embedded DejaVu Sans to guarantee peso glyph rendering */
            font-family: 'DejaVu Sans', 'Times New Roman', serif;
            font-size: 10px;
            line-height: 1.4;
            margin: 0;
            padding: 0;
            background: white !important;
        }

        .report-header {
            text-align: center;
            margin-bottom: 12px;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
            page-break-after: avoid;
        }

        .report-header h1 {
            font-size: 12px;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
        }

        .report-header p {
            font-size: 9px;
            margin: 3px 0;
        }

        .entity-info {
            text-align: center;
            margin: 12px 0;
            font-size: 9px;
            page-break-after: avoid;
        }

        .accountability-info {
            text-align: center;
            margin: 12px 0;
            font-size: 9px;
            padding: 6px;
            border: 1px solid #000;
            page-break-after: avoid;
        }

        .equipment-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7px;
            margin-bottom: 15px;
            border: 1px solid #000;
            table-layout: auto;
        }

        .equipment-table th {
            padding: 3px 1px;
            text-align: center;
            font-weight: bold;
            border: 1px solid #000;
            background: #f0f0f0;
            font-size: 7px;
            text-transform: uppercase;
            white-space: nowrap;
            vertical-align: middle;
        }

        .equipment-table td {
            padding: 3px 1px;
            border: 1px solid #000;
            font-size: 7px;
            word-wrap: break-word;
            overflow-wrap: break-word;
            vertical-align: middle;
        }

        .classification-header-row {
            background: #e0e0e0 !important;
        }

        .classification-header-row td {
            font-weight: bold;
            font-size: 8px;
            padding: 4px 1px;
            text-align: center;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .page-break {
            page-break-before: always;
        }

        .footer-info {
            margin-top: 15px;
            font-size: 8px;
            text-align: center;
            page-break-after: avoid;
        }

        .signature-section {
            margin-top: 20px;
            display: table;
            width: 100%;
        }

        .signature-line {
            display: table-cell;
            width: 50%;
            text-align: center;
            vertical-align: top;
        }

        .signature-line p {
            margin: 3px 0;
            font-size: 8px;
        }

        /* Ensure all content fits within page */
        * {
            box-sizing: border-box !important;
        }

        /* Prevent content overflow */
        .equipment-table {
            max-width: 100% !important;
        }
    </style>
</head>
<body>
    <div class="report-header">
        <h1>REPORT ON THE PHYSICAL COUNT OF PROPERTY, PLANT AND EQUIPMENT</h1>
<p>As of {!! $header['as_of'] ?: '______' !!}</p>
    </div>

    <div class="entity-info">
        <p><strong>Entity Name:</strong> {{ $header['entity_name'] }}</p>
        <p><strong>Fund Cluster:</strong> {{ $header['fund_cluster'] }}</p>
    </div>

    <div class="accountability-info">
        <p>For which {{ $header['accountable_person'] }}, {{ $header['position'] }}, {{ $header['office'] }} is accountable, having assumed such accountability on {{ isset($header['assumption_date']) && trim($header['assumption_date']) !== '' ? \Carbon\Carbon::parse($header['assumption_date'])->format('F d, Y') : '________________' }}.</p>
    </div>

    @if($groupedEquipment->count() > 0)
        <table class="equipment-table">
                <thead>
                    <tr>
                        <th rowspan="2">ARTICLE/ITEM</th>
                        <th rowspan="2">DESCRIPTION</th>
                        <th rowspan="2">PROPERTY NUMBER</th>
                        <th rowspan="2">UNIT OF MEASURE</th>
                        <th rowspan="2">UNIT VALUE</th>
                        <th rowspan="2">Acquisition<br>Date</th>
                        <th rowspan="2">QUANTITY per<br>PROPERTY CARD</th>
                        <th rowspan="2">QUANTITY per<br>PHYSICAL COUNT</th>
                        <th colspan="2" style="min-width: 50px;">SHORTAGE/<br>OVERAGE</th>
                        <th colspan="3">REMARKS</th>
                    </tr>
                    <tr>
                        <th>Quantity</th>
                        <th>Value</th>
                        <th>Person<br>Responsible</th>
                        <th>Responsibility<br>Center</th>
                        <th>Condition of<br>Properties</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($groupedEquipment as $classification => $equipmentItems)
                        <!-- Classification Header -->
                        <tr class="classification-header-row">
                            <td colspan="13">
                                {{ strtoupper($classification ?: 'UNCLASSIFIED EQUIPMENT') }}
                            </td>
                        </tr>

                        @php
                            $groupedByArticle = $equipmentItems->groupBy('article');
                        @endphp

                        @foreach($groupedByArticle as $article => $items)
                            @foreach($items as $index => $equipment)
                                <tr>
                                    @if($index === 0)
                                        <!-- Show article name only for first item -->
                                        <td rowspan="{{ $items->count() }}" style="vertical-align: middle; font-weight: bold;">
                                            {{ $article }}
                                        </td>
                                    @endif

                                    <td>{{ $equipment->description ?: '-' }}</td>
                                    <td class="text-center">{{ $equipment->property_number }}</td>
                                    <td class="text-center">{{ $equipment->unit_of_measurement }}</td>
                                    <td class="text-right">{{ number_format($equipment->unit_value, 2) }}</td>
                                    <td class="text-center">
                                        {{ $equipment->acquisition_date ? $equipment->acquisition_date->format('M-d') : '-' }}
                                    </td>
                                    <td class="text-center">1</td>
                                    <td class="text-center">1</td>
                                    <td class="text-center">-</td>
                                    <td class="text-center">-</td>
                                    <td class="text-center remarks-cell">
                                        {{ $equipment->responsible_person ?: 'Unknown / Book of the Accountant' }}
                                    </td>
                                    <td class="text-center remarks-cell">
                                        {{ $equipment->location ?: '-' }}
                                    </td>
                                    <td class="text-center">{{ $equipment->condition }}</td>
                                </tr>
                            @endforeach
                        @endforeach
                    @endforeach
                </tbody>
        </table>
    @endif

    <div class="footer-info">
        <p><strong>Certified Correct:</strong></p>
        <br><br>
        <p>___________________________________</p>
        <p>{{ $header['accountable_person'] }}</p>
        <p>{{ $header['position'] }}</p>
        <p>{{ $header['office'] }}</p>
    </div>
</body>
</html>
