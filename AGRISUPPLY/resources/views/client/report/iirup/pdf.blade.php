<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IIRUP - Inventory and Inspection Report of Unserviceable Property</title>
    <style>
        @font-face {
            font-family: 'DejaVu Sans';
            src: url('file://{{ str_replace('\\','/', public_path('fonts/DejaVuSans.ttf')) }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }
        @page {
            margin: 0.5cm;
            size: A4 landscape;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 0;
            line-height: 1.4;
            background: white !important;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            page-break-after: avoid;
        }
        .header h1 { font-size: 12px; font-weight: bold; margin: 0; }
        .header h2 { font-size: 10px; margin: 3px 0; }
        .report-info {
            margin-bottom: 15px;
            page-break-after: avoid;
        }
        .entity-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }
        .accountable-info {
            text-align: center;
            margin-bottom: 15px;
            page-break-after: avoid;
        }
        .table-container {
            overflow-x: auto;
            max-width: 100%;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 6px;
            table-layout: fixed;
        }
        th, td {
            border: 1px solid #000;
            padding: 2px 1px;
            text-align: center;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        th {
            background-color: #f0f0f0;
            font-weight: bold;
            font-size: 6px;
        }
        .footer {
            text-align: center;
            margin-top: 15px;
            font-weight: bold;
            page-break-after: avoid;
        }

        /* Ensure all content fits within page */
        * {
            box-sizing: border-box !important;
        }

        /* Prevent content overflow */
        .table-container, table {
            max-width: 100% !important;
        }

        @media print {
            /* Set proper page margins for complete visibility */
            @page {
                margin: 0.5cm;
                size: A4 landscape;
            }

            /* Reset body and page styles to match PDF */
            body {
                background: white !important;
                margin: 0 !important;
                padding: 0 !important;
                font-size: 12px !important;
                font-family: 'DejaVu Sans', Arial, sans-serif !important;
                line-height: 1.4 !important;
            }

            /* Hide all unnecessary elements */
            .sidebar,
            .header,
            .back-button,
            .filters-section,
            .print-button,
            .report-info,
            .accountability-info,
            .export-buttons,
            .export-fab,
            .fab-container,
            button,
            .btn,
            nav,
            footer,
            .no-print,
            .dashboard-header,
            .header-left,
            .header-right,
            .navigation,
            .brand-container,
            .notifications,
            .user-avatar,
            .user-info,
            .fab-print,
            .fab-pdf,
            .fab-excel,
            .sidebar *,
            .header *,
            .dashboard-header *,
            .header-left *,
            .header-right *,
            .navigation *,
            .notifications *,
            .user-profile *,
            .user-avatar *,
            .user-info *,
            .fab *,
            .fab-print *,
            .fab-pdf *,
            .fab-excel * {
                display: none !important;
            }

            /* Show only content area */
            .container {
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .details {
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
            }

            .ppes-content {
                padding: 10px !important;
                margin: 0 !important;
                width: 100% !important;
                box-sizing: border-box !important;
            }

            /* Style the header for print */
            .ppes-header {
                text-align: center;
                margin-bottom: 15px;
                padding-bottom: 8px;
                border-bottom: 2px solid #000;
                page-break-after: avoid;
            }

            .ppes-header h1 {
                font-size: 13px;
                margin: 2px 0;
                color: #000;
                font-weight: bold;
            }

            .ppes-header h2 {
                font-size: 11px;
                margin: 3px 0;
                color: #000;
            }

            /* Show report info for print */
            .report-info {
                display: block !important;
                margin: 15px 0;
                padding: 10px;
                background: white !important;
                border-radius: 0 !important;
                page-break-after: avoid;
            }

            .report-info p {
                margin: 0 0 8px 0;
                font-size: 11px;
                color: #000;
                font-weight: bold;
            }

            .header-grid {
                display: grid !important;
                grid-template-columns: repeat(4, 1fr) !important;
                gap: 15px !important;
                text-align: center !important;
            }

            .header-grid div p {
                margin: 0;
                font-size: 11px;
                color: #000;
            }

            /* Style the table for print */
            .report-table-container {
                padding: 0 !important;
                margin: 40px auto 0 auto !important;
                box-shadow: none !important;
                border-radius: 0 !important;
                width: 100% !important;
                overflow-x: visible !important;
            }

            .report-table {
                width: 100%;
                border-collapse: collapse;
                font-size: 6px;
                page-break-inside: auto;
                table-layout: fixed;
            }

            .report-table thead {
                display: table-header-group;
            }

            .report-table tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }

            .report-table th,
            .report-table td {
                border: 1px solid #000 !important;
                padding: 1px 0.5px !important;
                font-size: 6px !important;
                text-align: center;
                word-wrap: break-word;
                overflow-wrap: break-word;
            }

            .report-table th {
                background-color: #f0f0f0 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                font-weight: 600;
                font-size: 6px !important;
            }

            /* Set specific column widths for better fit */
            .report-table th:nth-child(1), .report-table td:nth-child(1) { width: 8%; } /* Date Acquired */
            .report-table th:nth-child(2), .report-table td:nth-child(2) { width: 15%; } /* Particulars */
            .report-table th:nth-child(3), .report-table td:nth-child(3) { width: 10%; } /* Property No. */
            .report-table th:nth-child(4), .report-table td:nth-child(4) { width: 4%; } /* Qty */
            .report-table th:nth-child(5), .report-table td:nth-child(5) { width: 8%; } /* Unit Cost */
            .report-table th:nth-child(6), .report-table td:nth-child(6) { width: 8%; } /* Total Cost */
            .report-table th:nth-child(7), .report-table td:nth-child(7) { width: 8%; } /* Accumulated Depreciation */
            .report-table th:nth-child(8), .report-table td:nth-child(8) { width: 8%; } /* Accumulated Impairment */
            .report-table th:nth-child(9), .report-table td:nth-child(9) { width: 8%; } /* Carrying Amount */
            .report-table th:nth-child(10), .report-table td:nth-child(10) { width: 8%; } /* Remarks */
            .report-table th:nth-child(11), .report-table td:nth-child(11) { width: 5%; } /* Sale */
            .report-table th:nth-child(12), .report-table td:nth-child(12) { width: 5%; } /* Transfer */
            .report-table th:nth-child(13), .report-table td:nth-child(13) { width: 5%; } /* Destruction */
            .report-table th:nth-child(14), .report-table td:nth-child(14) { width: 5%; } /* Others */
            .report-table th:nth-child(15), .report-table td:nth-child(15) { width: 5%; } /* Total */
            .report-table th:nth-child(16), .report-table td:nth-child(16) { width: 8%; } /* Appraised Value */
            .report-table th:nth-child(17), .report-table td:nth-child(17) { width: 6%; } /* OR No. */
            .report-table th:nth-child(18), .report-table td:nth-child(18) { width: 8%; } /* Amount */

            /* Ensure all content fits within page */
            * {
                box-sizing: border-box !important;
            }

            /* Prevent content overflow */
            .ppes-content * {
                max-width: 100% !important;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>INVENTORY AND INSPECTION REPORT OF UNSERVICEABLE PROPERTY</h1>
        <h2>(ATI-RTC I)</h2>
    </div>

    <div class="report-info">
        @if(isset($header['as_of']) && trim($header['as_of']) !== '')
        <p><strong>As of {!! e($header['as_of']) !!}</strong></p>
        @endif

        <div style="display:flex; justify-content:space-between; align-items:flex-start;">
            <div style="flex:1;">
                <p style="margin:0"><strong>Entity Name:</strong>
                    {!! isset($header['entity_name']) && trim($header['entity_name']) !== '' ? e($header['entity_name']) : '<span style="border-bottom:1px solid #000;padding:0 120px;display:inline-block;">&nbsp;</span>' !!}
                </p>
                <p style="margin:8px 0 0 0; text-align:center; font-weight:600;">
                    {!! isset($header['accountable_person']) && trim($header['accountable_person']) !== '' ? e($header['accountable_person']) : '<span style="border-bottom:1px solid #000;padding:0 120px;display:inline-block;">&nbsp;</span>' !!}
                </p>
                <p style="margin:4px 0 0 0; text-align:center; font-style:italic;">(Name of Accountable Officer)</p>
            </div>

            <div style="flex:1; text-align:center;">
                <p style="margin:0; font-weight:600;">{!! isset($header['position']) && trim($header['position']) !== '' ? e($header['position']) : '<span style="border-bottom:1px solid #000;padding:0 80px;display:inline-block;">&nbsp;</span>' !!}</p>
                <p style="margin:4px 0 0 0; font-style:italic;">(Designation)</p>
            </div>

            <div style="flex:1; text-align:right;">
                <p style="margin:0; font-weight:600;">{!! isset($header['office']) && trim($header['office']) !== '' ? e($header['office']) : '<span style="border-bottom:1px solid #000;padding:0 80px;display:inline-block;">&nbsp;</span>' !!}</p>
                <p style="margin:4px 0 0 0; font-style:italic;">(Station)</p>
                <p style="margin:8px 0 0 0;"><strong>Fund Cluster :</strong> {!! isset($header['fund_cluster']) && trim($header['fund_cluster']) !== '' ? e($header['fund_cluster']) : '<span style="border-bottom:1px solid #000; padding:0 18px;display:inline-block;">&nbsp;</span>' !!}</p>
            </div>
        </div>
    </div>

    <div class="report-table-container">
        <table class="report-table">
            <thead>
                <tr>
                    <th colspan="10">INVENTORY</th>
                    <th colspan="5">INSPECTION and DISPOSAL</th>
                    <th colspan="1">Appraised Value</th>
                    <th colspan="2">RECORD OF SALES</th>
                </tr>
                <tr>
                    <th>Date Acquired</th>
                    <th>Particulars/ Articles</th>
                    <th>Property No.</th>
                    <th>Qty</th>
                    <th>Unit Cost</th>
                    <th>Total Cost</th>
                    <th>Accumulated Depreciation</th>
                    <th>Accumulated Impairment Losses</th>
                    <th>Carrying Amount</th>
                    <th>Remarks</th>
                    <th colspan="5">DISPOSAL</th>
                    <th></th>
                    <th>OR No.</th>
                    <th>Amount</th>
                </tr>
                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th>Sale</th>
                    <th>Transfer</th>
                    <th>Destruction</th>
                    <th>Others (Specify)</th>
                    <th>Total</th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
                <tr>
                    <th>(1)</th>
                    <th>(2)</th>
                    <th>(3)</th>
                    <th>(4)</th>
                    <th>(5)</th>
                    <th>(6)</th>
                    <th>(7)</th>
                    <th>(8)</th>
                    <th>(9)</th>
                    <th>(10)</th>
                    <th>(11)</th>
                    <th>(12)</th>
                    <th>(13)</th>
                    <th>(14)</th>
                    <th>(15)</th>
                    <th>(16)</th>
                    <th>(17)</th>
                    <th>(18)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ppesItems as $item)
                <tr>
                    <td>{{ $item->date_acquired }}</td>
                    <td>{{ $item->particulars_articles }}</td>
                    <td>{{ $item->property_no }}</td>
                    <td>{{ $item->qty }}</td>
                    <td>₱ {{ number_format((float) ($item->unit_cost ?? 0), 2) }}</td>
                    <td>₱ {{ number_format((float) ($item->total_cost ?? 0), 2) }}</td>
                    <td>₱ {{ number_format((float) ($item->accumulated_depreciation ?? 0), 2) }}</td>
                    <td>₱ {{ number_format((float) ($item->accumulated_impairment_losses ?? 0), 2) }}</td>
                    <td>₱ {{ number_format((float) ($item->carrying_amount ?? 0), 2) }}</td>
                    <td>{{ $item->remarks }}</td>
                    <td>{{ $item->sale }}</td>
                    <td>{{ $item->transfer }}</td>
                    <td>{{ $item->destruction }}</td>
                    <td>{{ $item->others }}</td>
                    <td>{{ $item->total_disposal }}</td>
                    <td>₱ {{ number_format((float) ($item->appraised_value ?? 0), 2) }}</td>
                    <td>{{ $item->or_no }}</td>
                    <td>₱ {{ number_format((float) ($item->amount ?? 0), 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="18">No unserviceable equipment found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="footer">
        PANGASINAN
    </div>
</body>
</html>
