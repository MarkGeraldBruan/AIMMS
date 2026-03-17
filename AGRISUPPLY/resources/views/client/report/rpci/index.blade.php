<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RPCI - Report on the Physical Count of Inventories</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .rpci-content {
            padding: 20px;
            background: white;
            min-height: calc(100vh - 40px);
        }

        .report-header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #000;
        }

        .report-header h1 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
            color: #000;
        }

        .report-header p {
            margin: 5px 0;
        }

        .accountability-info {
            text-align: center;
            margin: 20px 0;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
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
            overflow-x: auto;
        }

        .report-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }

        .report-table th,
        .report-table td {
            border: 1px solid #ccc;
            padding: 8px 6px;
            text-align: center;
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

            /* Reset body and page styles to match PDF */
            body {
                background: white !important;
                margin: 0 !important;
                padding: 0 !important;
                font-size: 12px !important;
                font-family: 'DejaVu Sans', Arial, sans-serif !important;
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
            .export-btn,
            .fab,
            .fab-print,
            .fab-pdf,
            .fab-excel,
            button,
            .btn,
            nav,
            footer,
            .no-print,
            .user-profile,
            .system-title,
            .institute-name,
            .export-icons,
            .pdf-icon,
            .excel-icon,
            .dashboard-header,
            .header-left,
            .header-right,
            .navigation,
            .brand-container,
            .brand-logo,
            .title,
            .notifications,
            .user-profile,
            .user-avatar,
            .user-info,
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

            .rpci-content {
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
                margin: 20px auto 0 auto !important;
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

            /* Set specific column widths for better fit */
            .report-table th:nth-child(1), .report-table td:nth-child(1) { width: 10%; } /* Article */
            .report-table th:nth-child(2), .report-table td:nth-child(2) { width: 20%; } /* Description */
            .report-table th:nth-child(3), .report-table td:nth-child(3) { width: 10%; } /* Stock Number */
            .report-table th:nth-child(4), .report-table td:nth-child(4) { width: 8%; } /* Unit of Measure */
            .report-table th:nth-child(5), .report-table td:nth-child(5) { width: 10%; } /* Unit Value */
            .report-table th:nth-child(6), .report-table td:nth-child(6) { width: 8%; } /* Balance Per Card */
            .report-table th:nth-child(7), .report-table td:nth-child(7) { width: 8%; } /* On Hand Per Count */
            .report-table th:nth-child(8), .report-table td:nth-child(8) { width: 8%; } /* Shortage/Overage Quantity */
            .report-table th:nth-child(9), .report-table td:nth-child(9) { width: 10%; } /* Shortage/Overage Value */
            .report-table th:nth-child(10), .report-table td:nth-child(10) { width: 18%; } /* Remarks */

            /* Ensure all content fits within page */
            * {
                box-sizing: border-box !important;
            }

            /* Prevent content overflow */
            .rpci-content * {
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

        <div class="rpci-content">
            <a href="{{ route('client.reports.index') }}" class="back-button">
                <ion-icon name="arrow-back-outline"></ion-icon>
                Back to Reports
            </a>

            <div class="report-header">
                <h1>REPORT ON THE PHYSICAL COUNT OF INVENTORIES</h1>
                <h1>COMMON SUPPLIES</h1>
                <h1>(REGULAR)</h1>
                <p>As of {!! isset($header['as_of']) && trim($header['as_of']) !== '' ? e($header['as_of']) : '<span style="border-bottom:1px solid #000;padding:0 80px;display:inline-block;">&nbsp;</span>' !!}</p>
                <p>Fund Cluster: {!! isset($header['fund_cluster']) && trim($header['fund_cluster']) !== '' ? e($header['fund_cluster']) : '<span style="border-bottom:1px solid #000;padding:0 40px;display:inline-block;">&nbsp;</span>' !!}</p>
            </div>

            {{-- Header display section --}}
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

            <div class="accountability-info">
                <p>
                    For which
                    {!! isset($header['accountable_person']) && trim($header['accountable_person']) !== '' ? e($header['accountable_person']) : '<span style="border-bottom:1px solid #000;padding:0 90px;display:inline-block;">&nbsp;</span>' !!},
                    {!! isset($header['position']) && trim($header['position']) !== '' ? e($header['position']) : '<span style="border-bottom:1px solid #000;padding:0 90px;display:inline-block;">&nbsp;</span>' !!},
                    {!! isset($header['office']) && trim($header['office']) !== '' ? e($header['office']) : '<span style="border-bottom:1px solid #000;padding:0 90px;display:inline-block;">&nbsp;</span>' !!}
                    is accountable, having assumed such accountability on
                    {!! isset($header['assumption_date']) && trim($header['assumption_date']) !== '' ? e(\Carbon\Carbon::parse($header['assumption_date'])->format('F d, Y')) : '<span style="border-bottom:1px solid #000;padding:0 90px;display:inline-block;">&nbsp;</span>' !!}.
                </p>
            </div>
            {{-- Data filters --}}
            <div class="filters-section">
                <form method="GET" action="{{ route('client.report.rpci') }}" class="filters-form">
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
                        <a href="{{ route('client.report.rpci') }}" class="btn btn-secondary">
                            <i class="fas fa-redo"></i> Reset
                        </a>
                    </div>
                </form>
            </div>

            {{-- Header input form (mirrors PPES) --}}
            <div class="filters-section" style="margin-top:8px;">
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
                        <a href="{{ route('client.report.rpci') }}" class="btn btn-secondary">Reset</a>
                    </div>
                </form>
            </div>

            @if($rpciItems->count() > 0)
                <div class="report-table-container">
                    <table class="report-table">
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
                            @foreach($rpciItems as $item)
                                <tr>
                                    <td>{{ $item->article }}</td>
                                    <td>{{ $item->description }}</td>
                                    <td>{{ $item->stock_number }}</td>
                                    <td>{{ $item->unit_of_measure }}</td>
                                    <td>₱ {{ number_format($item->unit_value, 2) }}</td>
                                    <td>{{ $item->balance_per_card }}</td>
                                    <td>{{ $item->on_hand_per_count }}</td>
                                    <td>{{ $item->shortage_overage_quantity }}</td>
                                    <td>₱ {{ number_format($item->shortage_overage_value, 2) }}</td>
                                    <td>{{ $item->remarks }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @include('client.report._export_fab', [
                    // pass raw query params so user inputs (dates/text) are forwarded as-is
                    'excelUrl' => route('client.report.rpci.export.excel', request()->query()),
                    'pdfUrl' => route('client.report.rpci.export.pdf', request()->query())
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
