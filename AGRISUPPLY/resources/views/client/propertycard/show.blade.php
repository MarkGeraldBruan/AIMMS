<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Card - {{ $equipment->article }}</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/supplies.css') }}">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <div class="container">
        @include('layouts.core.sidebar')
        <div class="details">
            @include('layouts.core.header')
            
            <div class="supplies-container">
                <!-- Header Section -->
                <div class="form-header">
                    <div class="header-top">
                        <a href="{{ route('client.propertycard.index') }}" class="back-button">
                            <i class="fas fa-arrow-left"></i>
                            Back to Property Card
                        </a>

                        <div class="action-buttons">
                            <a href="{{ route('client.propertycard.export.excel', $equipment->id) }}" class="btn btn-primary">
                                <i class="fas fa-download"></i>
                                Export
                            </a>
                        </div>
                    </div>

                    <div class="property-summary">
                        <div class="summary-yellow">
                            <strong>Property, Plant and Equipment:</strong> {{ $equipment->article }}
                        </div>
                        <div class="summary-row">
                            <div class="summary-left">
                                <strong>Description:</strong> {{ $equipment->description ?: 'N/A' }}
                            </div>
                            <div class="summary-right">
                                <strong>Property Number:</strong> {{ $equipment->property_number }}
                            </div>
                        </div>

                    </div>
                </div>
                
                <!-- Property Card Table -->
                <div class="supplies-table-container">
                    <table class="property-card-table">
                        <thead>
                            <tr>
                                <th rowspan="2" style="width: 12%;">Date</th>
                                <th rowspan="2" style="width: 15%;">Reference/<br>PAR No.</th>
                                <th colspan="2" style="width: 16%;">Receipt</th>
                                <th rowspan="2" style="width: 25%;">Office/Officer</th>
                                <th rowspan="2" style="width: 10%;">Balance<br>Qty.</th>
                                <th rowspan="2" style="width: 12%;">Amount</th>
                                <th rowspan="2" style="width: 10%;">Remarks</th>
                            </tr>
                            <tr>
                                <th style="width: 8%;">Qty.</th>
                                <th style="width: 8%;">Qty.</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="data-row">
                                <td style="text-align: center;">
                                    {{ $equipment->acquisition_date ? $equipment->acquisition_date->format('M d, Y') : $equipment->created_at->format('M d, Y') }}
                                </td>
                                <td style="text-align: center;">
                                    {{ $equipment->property_number }}
                                </td>
                                <td style="text-align: center;">1</td>
                                <td style="text-align: center;">1</td>
                                <td>
                                    @if($equipment->location || $equipment->responsible_person)
                                        @if($equipment->location)
                                            {{ $equipment->location }}
                                        @endif
                                        @if($equipment->location && $equipment->responsible_person)
                                            /
                                        @endif
                                        @if($equipment->responsible_person)
                                            {{ $equipment->responsible_person }}
                                        @endif
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td style="text-align: center;">1</td>
                                <td style="text-align: right;">
                                    {{ number_format($equipment->unit_value, 2) }}
                                </td>
                                <td style="text-align: center;">
                                    {{ $equipment->condition }}
                                </td>
                            </tr>
                            <!-- Empty rows for future entries -->
                            @for($i = 0; $i < 15; $i++)
                            <tr class="empty-row">
                                <td>&nbsp;</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>

            </div>
        </div>   
    </div>

    <style>
        .back-button {
            display: flex;
            align-items: center;
            gap: 8px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            margin-bottom: 15px;
            transition: color 0.3s ease;
        }

        .back-button:hover {
            color: white;
        }

        .property-card-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .card-title {
            font-size: 20px;
            font-weight: 700;
            color: white;
            margin: 0 0 15px 0;
            letter-spacing: 2px;
        }

        .header-info {
            background: rgba(255,255,255,0.1);
            padding: 12px;
            border-radius: 8px;
        }

        .header-row {
            display: flex;
            justify-content: space-between;
            gap: 20px;
        }

        .header-item {
            color: white;
            font-size: 13px;
        }

        .property-summary {
            background: rgba(255,255,255,0.1);
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
        }

        .summary-yellow {
            background: #ffc107;
            padding: 10px 15px;
            border-radius: 6px;
            margin-bottom: 10px;
            color: #212529;
            font-size: 14px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            color: white;
            font-size: 13px;
        }

        .summary-left {
            flex: 1;
        }

        .summary-right {
            min-width: 250px;
            text-align: right;
        }

        .property-card-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            font-size: 13px;
        }

        .property-card-table th {
            background: #f8f9fa;
            padding: 10px 8px;
            text-align: center;
            font-weight: 600;
            color: #333;
            border: 1px solid #dee2e6;
            font-size: 12px;
        }

        .property-card-table td {
            padding: 8px;
            border: 1px solid #dee2e6;
            font-size: 12px;
        }

        .property-card-table .data-row {
            background: #fff;
        }

        .property-card-table .empty-row {
            height: 35px;
        }

        .property-card-table .empty-row td {
            border-left: 1px solid #dee2e6;
            border-right: 1px solid #dee2e6;
            border-bottom: 1px solid #e9ecef;
        }

        .table-header-section {
            background: #f8f9fa;
            padding: 15px 20px;
            border-bottom: 2px solid #dee2e6;
        }

        .section-title {
            margin: 0;
            font-size: 18px;
            font-weight: 700;
            color: #333;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        .info-table th,
        .info-table td {
            border: 1px solid #dee2e6;
            padding: 12px;
            font-size: 14px;
        }

        .info-table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #495057;
            text-align: left;
        }

        .info-table td {
            color: #333;
        }

        @media print {
            .back-button,
            .supplies-header,
            .sidebar,
            .header {
                display: none;
            }

            .supplies-container {
                box-shadow: none;
                border-radius: 0;
            }

            .property-card-table {
                page-break-inside: avoid;
            }
        }

        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        @media (max-width: 768px) {
            .header-row,
            .summary-row {
                flex-direction: column;
            }

            .summary-right {
                text-align: left;
                min-width: unset;
            }

            .property-card-table {
                font-size: 10px;
            }

            .property-card-table th,
            .property-card-table td {
                padding: 5px 3px;
            }

            .header-top {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
        }
    </style>
</body>
</html>