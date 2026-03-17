<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RSMI - Report of Supplies and Materials Issued</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .rsmi-content {
            padding: 20px;
            background: white;
        }

        .report-header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #000;
        }

        .report-header h1 {
            color: #000;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background-color: #296218;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            margin-bottom: 20px;
        }

        .back-button:hover {
            background-color: #1e4612;
            color: white;
        }

        .filters-section {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
        }

        .filters-form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 12px;
            align-items: end;
        }

        .filter-group label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: #495057;
            margin-bottom: 5px;
        }

        .filter-group select,
        .filter-group input {
            width: 100%;
            padding: 6px 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 13px;
        }

        .filter-actions {
            display: flex;
            gap: 8px;
        }

        .btn {
            padding: 6px 14px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: #296218;
            color: white;
        }

        .btn-primary:hover {
            background: #1e4612;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .report-table-container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .report-table {
            width: 100%;
            border-collapse: collapse;
        }

        .report-table th,
        .report-table td {
            border: 1px solid #ccc;
            padding: 8px 6px;
            text-align: center;
            font-size: 11px;
        }

        .report-table th {
            background-color: #e8f5e9;
            color: #000;
            font-weight: 600;
        }

        .export-buttons {
            display: flex;
            gap: 10px;
            margin: 20px 0;
        }

        .export-btn {
            background-color: #27ae60;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .export-btn.pdf {
            background-color: #e74c3c;
        }

        .export-btn.excel {
            background-color: #3498db;
        }

        .export-btn:hover {
            transform: translateY(-1px);
            opacity: 0.9;
        }

        .print-button {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: #296218;
            color: white;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            border: none;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            cursor: pointer;
            font-size: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .print-button:hover {
            background: #1e4612;
            transform: scale(1.05);
        }

        .recap-container {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            margin-top: 30px;
        }

        .recap {
            width: 48%;
            background: white;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .recap table {
            width: 100%;
            border-collapse: collapse;
        }

        .recap th,
        .recap td {
            border: 1px solid #ccc;
            padding: 8px 6px;
            text-align: center;
            font-size: 11px;
        }

        .recap th {
            background-color: #f8f9fa;
            font-weight: 600;
        }

        .empty-state {
            text-align: center;
            padding: 40px;
            color: #6c757d;
        }

        .empty-state i {
            font-size: 48px;
            margin-bottom: 15px;
        }

        .empty-state h3 {
            margin-bottom: 10px;
        }

        @media print {
            /* Set proper page margins for complete visibility */
            @page {
                margin: 0.5cm;
                size: A4 landscape;
            }

            * {
                overflow: visible !important;
            }

            html, body {
                width: 100% !important;
                max-width: none !important;
                overflow: visible !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            /* Reset body and page styles */
            body {
                background: white !important;
                margin: 0 !important;
                padding: 0 !important;
                font-family: 'DejaVu Sans', Arial, sans-serif !important;
                font-size: 12px !important;
                line-height: 1.4 !important;
                overflow: visible !important;
            }

            /* Hide all unnecessary elements */
            .sidebar,
            .header,
            .back-button,
            .filters-section,
            .print-button,
            .report-info,
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
                overflow: visible !important;
            }

            .details {
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                left: 0 !important;
                overflow: visible !important;
                background: white !important;
            }

            .rsmi-content {
                padding: 10px !important;
                margin: 0 !important;
                width: 100% !important;
                box-sizing: border-box !important;
                overflow: visible !important;
            }

            /* Style the header for print */
            .report-header {
                text-align: center;
                margin-bottom: 15px;
                padding-bottom: 8px;
                border-bottom: 2px solid #000;
                page-break-after: avoid;
            }

            .report-header h1 {
                font-size: 14px;
                margin: 2px 0;
                color: #000;
                font-weight: bold;
            }

            .report-header p {
                font-size: 12px;
                margin: 3px 0;
            }

            /* Show accountability info for print */
            .accountability-info {
                display: block !important;
                text-align: center;
                margin: 15px 0;
                padding: 10px;
                background: white !important;
                border-radius: 0 !important;
                page-break-after: avoid;
            }

            .accountability-info p {
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
                max-width: none !important;
                overflow: visible !important;
                overflow-x: visible !important;
                display: block !important;
            }

            .report-table {
                width: 100% !important;
                max-width: none !important;
                border-collapse: collapse !important;
                font-size: 6px;
                page-break-inside: auto;
                table-layout: auto !important;
                overflow: visible !important;
                overflow-x: visible !important;
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
                overflow: visible;
                white-space: normal;
            }

            .report-table th {
                background-color: #f0f0f0 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                font-weight: 600;
                font-size: 6px !important;
            }

            /* Set specific column widths for RSMI table */
            .report-table th:nth-child(1), .report-table td:nth-child(1) { width: 10%; } /* RIS No. */
            .report-table th:nth-child(2), .report-table td:nth-child(2) { width: 12%; } /* Responsibility Center Code */
            .report-table th:nth-child(3), .report-table td:nth-child(3) { width: 8%; } /* Stock No. */
            .report-table th:nth-child(4), .report-table td:nth-child(4) { width: 12%; } /* Item */
            .report-table th:nth-child(5), .report-table td:nth-child(5) { width: 15%; } /* Description */
            .report-table th:nth-child(6), .report-table td:nth-child(6) { width: 6%; } /* Unit */
            .report-table th:nth-child(7), .report-table td:nth-child(7) { width: 10%; } /* Quantity Issued */
            .report-table th:nth-child(8), .report-table td:nth-child(8) { width: 10%; } /* Unit Cost */
            .report-table th:nth-child(9), .report-table td:nth-child(9) { width: 17%; } /* Amount */

            /* Style recap tables for print */
            .recap-container {
                display: flex;
                justify-content: space-between;
                gap: 15px;
                margin-top: 15px;
                page-break-inside: avoid;
            }

            .recap {
                width: 48%;
                padding: 0 !important;
                box-shadow: none !important;
                border-radius: 0 !important;
            }

            .recap p {
                font-size: 8px;
                font-weight: 600;
                margin-bottom: 3px;
            }

            .recap table {
                width: 100%;
                border-collapse: collapse;
                font-size: 6px;
            }

            .recap th,
            .recap td {
                border: 1px solid #000 !important;
                padding: 1px 0.5px !important;
                font-size: 5px !important;
                text-align: center;
                word-wrap: break-word;
            }

            .recap th {
                background-color: #f8f9fa !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                font-weight: 600;
            }

            /* Ensure all content fits within page */
            * {
                box-sizing: border-box !important;
            }

            /* Prevent content overflow */
            .rsmi-content * {
                max-width: 100% !important;
            }
        }
    </style>
</head>
<body>
<div class="container">
    @include('layouts.core.sidebar')
    <div class="details">
        @include('layouts.core.header')

        <div class="rsmi-content">
            <a href="{{ route('client.reports.index') }}" class="back-button">
                <ion-icon name="arrow-back-outline"></ion-icon>
                Back to Reports
            </a>

            <div class="report-header">
                <h1>Republic of the Philippines</h1>
                <h1>{!! isset($header['entity_name']) && trim($header['entity_name']) !== '' ? e($header['entity_name']) : '______' !!}</h1>
                <h1>Report of Supplies and Materials Issued</h1>
                <p>For the Month of {!! isset($header['as_of']) && trim($header['as_of']) !== '' ? e($header['as_of']) : '<span style="border-bottom:1px solid #000;padding:0 80px;display:inline-block;">&nbsp;</span>' !!}</p>
                <p>Fund Cluster: {!! isset($header['fund_cluster']) && trim($header['fund_cluster']) !== '' ? e($header['fund_cluster']) : '<span style="border-bottom:1px solid #000;padding:0 40px;display:inline-block;">&nbsp;</span>' !!}</p>
            </div>

            <div class="accountability-info" style="text-align: center; margin: 20px 0; padding: 15px; background: #f8f9fa; border-radius: 8px;">
                <p>
                    For which
                    {!! isset($header['accountable_person']) && trim($header['accountable_person']) !== '' ? e($header['accountable_person']) : '<span style="border-bottom:1px solid #000;padding:0 120px;display:inline-block;">&nbsp;</span>' !!},
                    {!! isset($header['position']) && trim($header['position']) !== '' ? e($header['position']) : '<span style="border-bottom:1px solid #000;padding:0 120px;display:inline-block;">&nbsp;</span>' !!},
                    {!! isset($header['office']) && trim($header['office']) !== '' ? e($header['office']) : '<span style="border-bottom:1px solid #000;padding:0 120px;display:inline-block;">&nbsp;</span>' !!}
                    is accountable, having assumed such accountability on
                    {!! isset($header['assumption_date']) && trim($header['assumption_date']) !== '' ? e(\Carbon\Carbon::parse($header['assumption_date'])->format('F d, Y')) : '<span style="border-bottom:1px solid #000;padding:0 120px;display:inline-block;">&nbsp;</span>' !!}.
                </p>
            </div>

            <div class="report-info" style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 20px;">
                <div class="header-grid" style="display:grid;grid-template-columns: repeat(4, 1fr);gap: 20px;text-align:center;">
                    <div>
                        <p style="margin:0; font-weight:600;">Entity Name:</p>
                        <p style="margin:8px 0 0 0; font-weight:600;">
                            {!! isset($header['entity_name']) && trim($header['entity_name']) !== '' ? e($header['entity_name']) : '<span style="border-bottom:1px solid #000;padding:0 50px;display:inline-block;">&nbsp;</span>' !!}
                        </p>
                    </div>

                    <div>
                        <p style="margin:0; font-weight:600;">Accountable Officer:</p>
                        <p style="margin:8px 0 0 0; font-weight:600;">
                            {!! isset($header['accountable_person']) && trim($header['accountable_person']) !== '' ? e($header['accountable_person']) : '<span style="border-bottom:1px solid #000;padding:0 50px;display:inline-block;">&nbsp;</span>' !!}
                        </p>
                        <p style="margin:4px 0 0 0; font-style:italic;">(Name)</p>
                    </div>

                    <div>
                        <p style="margin:0; font-weight:600;">Position:</p>
                        <p style="margin:8px 0 0 0; font-weight:600;">
                            {!! isset($header['position']) && trim($header['position']) !== '' ? e($header['position']) : '<span style="border-bottom:1px solid #000;padding:0 50px;display:inline-block;">&nbsp;</span>' !!}
                        </p>
                        <p style="margin:4px 0 0 0; font-style:italic;">(Designation)</p>
                    </div>

                    <div>
                        <p style="margin:0; font-weight:600;">Office:</p>
                        <p style="margin:8px 0 0 0; font-weight:600;">
                            {!! isset($header['office']) && trim($header['office']) !== '' ? e($header['office']) : '<span style="border-bottom:1px solid #000;padding:0 50px;display:inline-block;">&nbsp;</span>' !!}
                        </p>
                        <p style="margin:4px 0 0 0; font-style:italic;">(Station)</p>
                        <p style="margin:8px 0 0 0; font-weight:600;">Fund Cluster: {!! isset($header['fund_cluster']) && trim($header['fund_cluster']) !== '' ? '<span style="border-bottom:1px solid #000; padding:0 18px;">' . e($header['fund_cluster']) . '</span>' : '<span style="border-bottom:1px solid #000;padding:0 18px;display:inline-block;">&nbsp;</span>' !!}</p>
                    </div>
                </div>
            </div>

            {{-- Data filters --}}
            <div class="filters-section">
                <form method="GET" action="{{ route('client.report.rsmi') }}" class="filters-form">
                        <div class="filter-group">
                        <label for="date_from">Date From</label>
                        <input type="date" id="date_from" name="date_from" value="{{ request('date_from') }}">
                        </div>

                        <div class="filter-group">
                        <label for="date_to">Date To</label>
                        <input type="date" id="date_to" name="date_to" value="{{ request('date_to') }}">
                        </div>
                    <div class="filter-group">
                        <label for="description">Description</label>
                        <select id="description" name="description">
                            <option value="">All Descriptions</option>
                            @foreach($descriptions as $desc)
                                <option value="{{ $desc }}" {{ request('description') == $desc ? 'selected' : '' }}>
                                    {{ $desc }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="status">Status</label>
                        <select id="status" name="status">
                            <option value="">All</option>
                            <option value="issued" {{ request('status') == 'issued' ? 'selected' : '' }}>Issued</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        </select>
                    </div>
                    <div class="filter-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter"></i> Apply Filters
                        </button>
                        <a href="{{ route('client.report.rsmi') }}" class="btn btn-secondary">
                            <i class="fas fa-redo"></i> Reset
                        </a>
                    </div>
                </form>
            </div>

            {{-- Header input form --}}
            <div class="filters-section">
                <form method="get" class="filters-form">
                    {{-- preserve current filters as hidden inputs --}}
                    <input type="hidden" name="date_from" value="{{ request('date_from') }}">
                    <input type="hidden" name="date_to" value="{{ request('date_to') }}">
                    <input type="hidden" name="description" value="{{ request('description') }}">
                    <input type="hidden" name="status" value="{{ request('status') }}">

                    <div class="filter-group">
                        <label>As of</label>
<input type="date" name="as_of" value="{{ request('as_of') ?? '' }}">
                    </div>
                    <div class="filter-group">
                        <label>Entity Name</label>
                        <input type="text" name="entity_name" value="{{ request('entity_name') ?? '' }}">
                    </div>
                    <div class="filter-group">
                        <label>Fund Cluster</label>
                        <input type="text" name="fund_cluster" value="{{ request('fund_cluster') ?? '' }}">
                    </div>
                    <div class="filter-group">
                        <label>Accountable Person</label>
                        <input type="text" name="accountable_person" value="{{ request('accountable_person') ?? '' }}">
                    </div>
                    <div class="filter-group">
                        <label>Position</label>
                        <input type="text" name="position" value="{{ request('position') ?? '' }}">
                    </div>
                    <div class="filter-group">
                        <label>Office</label>
                        <input type="text" name="office" value="{{ request('office') ?? '' }}">
                    </div>
                    <div class="filter-group">
                        <label>Assumption Date</label>
                        <input type="date" name="assumption_date" value="{{ request('assumption_date') ?? '' }}">
                    </div>
                    <div class="filter-group">
                        <button type="submit" class="btn btn-primary">Apply Header</button>
                        <a href="{{ route('client.report.rsmi') }}" class="btn btn-secondary">Reset</a>
                    </div>
                </form>
            </div>

            @if($rsmiItems->count() > 0)
                <div class="report-table-container">
                    <table class="report-table">
                        <thead>
                            <tr>
                                <th>RIS No.</th>
                                <th>Responsibility Center Code</th>
                                <th>Stock No.</th>
                                <th>Item</th>
                                <th>Description</th>
                                <th>Unit</th>
                                <th>Quantity Issued</th>
                                <th>Unit Cost</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rsmiItems as $item)
                                <tr>
                                    <td>{{ $item->issue_no }}</td>
                                    <td>{{ $item->responsibility_center }}</td>
                                    <td>{{ $item->stock_no }}</td>
                                    <td>{{ $item->item }}</td>
                                    <td>{{ $item->description }}</td>
                                    <td>{{ $item->unit }}</td>
                                    <td>{{ $item->quantity_issued }}</td>
                                    <td>₱ {{ number_format($item->unit_cost, 2) }}</td>
                                    <td>₱ {{ number_format($item->amount, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="recap-container">
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
                    <div class="recap">
                        <p><strong>Recapitulation:</strong></p>
                        <table>
                            <thead>
                                <tr>
                                    <th>Total Quantity</th>
                                    <th>Total Cost</th>
                                    <th>UACS Object Code</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ $recapRight['total_quantity'] }}</td>
                                    <td>₱ {{ number_format($recapRight['total_cost'], 2) }}</td>
                                    <td>{{ $recapRight['uacs_code'] }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                @include('client.report._export_fab', [
                    'excelUrl' => route('client.report.rsmi.export.excel', request()->query()),
                    'pdfUrl' => route('client.report.rsmi.export.pdf', request()->query())
                ])
            @else
                <div class="empty-state">
                    <i class="fas fa-box-open"></i>
                    <h3>No Supplies Found</h3>
                    <p>There are no supplies records to display. Try adjusting your filters or add supplies first.</p>
                </div>
            @endif
        </div>
    </div>
</div>
</body>
</html>
