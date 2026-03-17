<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Card - {{ $supply->name }}</title>
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
                        <a href="{{ route('client.stockcard.index') }}" class="back-button">
                            <i class="fas fa-arrow-left"></i>
                            Back to Stock Card
                        </a>

                        <div class="action-buttons">
                            <a href="{{ route('client.stockcard.export.excel', $supply->id) }}" class="btn btn-primary">
                                <i class="fas fa-download"></i>
                                Export
                            </a>
                        </div>
                    </div>

                    <div class="supply-info-header">
                        <div class="supply-title">
                            <h2>#{{ str_pad($supply->id, 4, '0', STR_PAD_LEFT) }} - {{ $supply->name }}</h2>
                            @if($supply->description)
                                <p class="supply-description">{{ $supply->description }}</p>
                            @endif
                        </div>
                        <div class="current-stock">
                            <div class="stock-label">Current Stock</div>
                            <div class="stock-value {{ $supply->quantity <= $supply->minimum_stock ? 'low-stock' : 'normal-stock' }}">
                                {{ $supply->quantity }} {{ $supply->unit }}
                            </div>
                            @if($supply->quantity <= $supply->minimum_stock)
                                <div class="low-stock-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    Low Stock Alert
                                </div>
                            @endif
                        </div>

                    </div>
                </div>

                <div class="supply-details">
                        <div class="detail-item">
                            <label>Category:</label>
                            <span>{{ $supply->category ?: 'Uncategorized' }}</span>
                        </div>
                        <div class="detail-item">
                            <label>Unit of Measurement:</label>
                            <span>{{ $supply->unit }}</span>
                        </div>
                        @if($supply->minimum_stock)
                        <div class="detail-item">
                            <label>Minimum Stock:</label>
                            <span>{{ $supply->minimum_stock }} {{ $supply->unit }}</span>
                        </div>
                        @endif
                    </div>
                </div>
                
                <!-- Stock Movements Table -->
                <div class="supplies-table-container">
                    @if($movements->count() > 0)
                        <table class="stock-card-table">
                            <thead>
                                <tr>
                                    <th rowspan="2" style="width: 12%;">Date</th>
                                    <th rowspan="2" style="width: 14%;">Reference</th>
                                    <th rowspan="2" style="width: 10%;">Receipt<br>Qty.</th>
                                    <th colspan="2" style="width: 50%;">Issue</th>
                                    <th rowspan="2" style="width: 14%;">Balance<br>Qty.</th>
                                </tr>
                                <tr>
                                    <th style="width: 10%;">Qty.</th>
                                    <th style="width: 40%;">Office</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($movements as $movement)
                                    <tr class="{{ $movement->type === 'in' ? 'stock-in-row' : '' }}">
                                        <td>{{ $movement->created_at->format('F d, Y') }}</td>
                                        <td>{{ $movement->reference }}</td>
                                        @if($movement->type === 'in')
                                            <!-- Stock In: Fill Receipt column with + sign -->
                                            <td style="text-align: center;" class="stock-in-cell">
                                                <span class="quantity-badge positive">+{{ $movement->quantity }}</span>
                                            </td>
                                            <!-- Issue columns empty -->
                                            <td style="text-align: center;"></td>
                                            <td>{{ $movement->notes ?: 'Balance as of ' . $movement->created_at->format('F Y') }}</td>
                                        @else
                                            <!-- Stock Out: Receipt column empty -->
                                            <td style="text-align: center;"></td>
                                            <!-- Fill Issue columns with - sign -->
                                            <td style="text-align: center;" class="stock-out-cell">
                                                <span class="quantity-badge negative">-{{ $movement->quantity }}</span>
                                            </td>
                                            <td>{{ $movement->notes ?: 'Issued' }}</td>
                                        @endif
                                        <!-- Balance column -->
                                        <td style="text-align: center;">{{ $movement->balance_after }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        
                        <!-- Pagination -->
                        @if($movements->hasPages())
                            <div class="pagination">
                                {{ $movements->links() }}
                            </div>
                        @endif
                    @else
                        <div class="empty-state">
                            <i class="fas fa-history"></i>
                            <h3>No stock movements found</h3>
                            <p>No stock movements have been recorded for this item yet.</p>
                            <div class="empty-actions">
                                <a href="{{ route('client.stockcard.stock-in') }}?supply_id={{ $supply->id }}" class="btn btn-success">
                                    <i class="fas fa-plus"></i>
                                    Add Stock
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>   
    </div>

    <style>
        .supplies-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }

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

        .supply-info-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            border: 2px solid #dee2e6;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .supply-info-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
            gap: 20px;
        }

        .supply-title h2 {
            margin: 0 0 8px 0;
            color: #dee2e6;
            font-size: 24px;
            font-weight: 700;
        }

        .supply-description {
            margin: 0;
            color: #6c757d;
            font-size: 16px;
            line-height: 1.5;
        }

        .current-stock {
            text-align: center;
            min-width: 180px;
        }

        .stock-label {
            font-size: 12px;
            color: #dee2e6;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .stock-value {
            font-size: 28px;
            font-weight: 800;
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 8px;
        }

        .stock-value.normal-stock {
            color: #28a745;
            background: rgba(40, 167, 69, 0.1);
            border: 2px solid #28a745;
        }

        .stock-value.low-stock {
            color: #dc3545;
            background: rgba(220, 53, 69, 0.1);
            border: 2px solid #dc3545;
        }

        .low-stock-warning {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            color: #dc3545;
            font-size: 12px;
            font-weight: 600;
            animation: pulse 2s infinite;
        }

        .supply-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
        }

        .detail-item {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .detail-item label {
            font-size: 12px;
            color: #dee2e6;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .detail-item span {
            color: #dee2e6;
            font-weight: 500;
            font-size: 16px;
        }

        /* Stock Card Table Styles */
        .stock-card-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            font-size: 13px;
        }

        .stock-card-table thead tr:first-child th {
            background: #f8f9fa;
            padding: 12px 8px;
            text-align: center;
            font-weight: 700;
            color: #333;
            border: 1px solid #dee2e6;
            font-size: 13px;
        }

        .stock-card-table thead tr:last-child th {
            background: #f8f9fa;
            padding: 10px 8px;
            text-align: center;
            font-weight: 600;
            color: #495057;
            border: 1px solid #dee2e6;
            font-size: 12px;
        }

        /* Empty header cells for Date and Reference */
        .stock-card-table thead tr:last-child th:first-child,
        .stock-card-table thead tr:last-child th:nth-child(2) {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
        }

        .stock-card-table tbody td {
            padding: 10px 12px;
            border: 1px solid #dee2e6;
            color: #333;
            vertical-align: middle;
        }

        .stock-card-table tbody tr {
            background: white;
            transition: background-color 0.2s ease;
        }

        /* Yellow highlight for stock-in rows */
        .stock-card-table tbody tr.stock-in-row {
            background: #ffeb3b;
        }

        .stock-card-table tbody tr.stock-in-row td {
            border-color: #fdd835;
        }

        .stock-card-table tbody tr:hover {
            background: #f8f9fa;
        }

        .stock-card-table tbody tr.stock-in-row:hover {
            background: #ffe821;
        }

        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
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

            .stock-card-table {
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
            .supplies-header {
                flex-direction: column;
                align-items: stretch;
            }

            .supply-info-header {
                flex-direction: column;
                text-align: center;
            }

            .current-stock {
                min-width: unset;
            }

            .supply-details {
                grid-template-columns: 1fr;
            }

            .stock-card-table {
                font-size: 11px;
            }

            .stock-card-table thead th,
            .stock-card-table tbody td {
                padding: 6px 4px;
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