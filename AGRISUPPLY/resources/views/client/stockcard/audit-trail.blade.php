<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Audit Trail</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/supplies.css') }}">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container">
        @include('layouts.core.sidebar')
        <div class="details">
            @include('layouts.core.header')
            
            <div class="supplies-container">
                <div class="supplies-header">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                        <h1 class="supplies-title" style="margin: 0;">
                            <i class="fas fa-history"></i>
                            Stock Audit Trail
                        </h1>
                        
                        <!-- View Toggle Buttons -->
                        <div class="view-toggle">
                            <button class="toggle-btn active" data-view="table">
                                <i class="fas fa-table"></i> Table
                            </button>
                            <button class="toggle-btn" data-view="chart">
                                <i class="fas fa-chart-bar"></i> Charts
                            </button>
                        </div>
                    </div>

                    <div class="filter-row">
                        <div style="display: flex; flex-direction: column; gap: 2px;">
                            <label for="dateFrom" style="font-size: 11px; font-weight: 600; color: rgba(255,255,255,0.9);">Date From (mm/dd/yy)</label>
                            <input type="date" id="dateFrom" class="form-control"
                                   value="{{ request('date_from') }}">
                        </div>

                        <div style="display: flex; flex-direction: column; gap: 2px;">
                            <label for="dateTo" style="font-size: 11px; font-weight: 600; color: rgba(255,255,255,0.9);">Date To (mm/dd/yy)</label>
                            <input type="date" id="dateTo" class="form-control"
                                   value="{{ request('date_to') }}">
                        </div>

                        <select id="userFilter" class="form-select">
                            <option value="">All Users</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>

                        <select id="actionFilter" class="form-select">
                            <option value="">All Actions</option>
                            <option value="stock_in" {{ request('action_type') == 'stock_in' ? 'selected' : '' }}>Stock In</option>
                            <option value="stock_out" {{ request('action_type') == 'stock_out' ? 'selected' : '' }}>Stock Out</option>
                        </select>

                        <button id="clearFilters" class="btn-clear">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>

                <!-- Table View -->
                <div id="tableView" class="supplies-table-container">
                    @if($auditTrails->count() > 0)
                        <table class="supplies-table">
                            <thead>
                                <tr>
                                    <th style="width: 15%;">Date & Time</th>
                                    <th style="width: 20%;">User</th>
                                    <th style="width: 30%;">Supply Item</th>
                                    <th style="width: 15%; text-align: center;">Action</th>
                                    <th style="width: 20%; text-align: center;">Quantity</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($auditTrails as $audit)
                                    <tr>
                                        <td>
                                            <div style="font-weight: 600; color: #333;">
                                                {{ $audit->created_at->format('M d, Y') }}
                                            </div>
                                            <div style="font-size: 12px; color: #6c757d;">
                                                {{ $audit->created_at->format('h:i A') }}
                                            </div>
                                        </td>
                                        <td>
                                            <div style="font-weight: 600; color: #333;">
                                                {{ $audit->user->name }}
                                            </div>
                                            <div style="font-size: 12px; color: #6c757d;">
                                                {{ $audit->user->email }}
                                            </div>
                                        </td>
                                        <td>
                                            <div style="font-weight: 600; color: #333;">
                                                {{ $audit->supply->name }}
                                            </div>
                                            @if($audit->supply->description)
                                                <div style="font-size: 12px; color: #6c757d;">
                                                    {{ Str::limit($audit->supply->description, 40) }}
                                                </div>
                                            @endif
                                        </td>
                                        <td style="text-align: center;">
                                            @if($audit->action_type === 'stock_in')
                                                <span class="badge badge-success">
                                                    <i class="fas fa-arrow-down"></i>
                                                    Stock In
                                                </span>
                                            @else
                                                <span class="badge badge-danger">
                                                    <i class="fas fa-arrow-up"></i>
                                                    Stock Out
                                                </span>
                                            @endif
                                        </td>
                                        <td style="text-align: center;">
                                            <div style="font-weight: 700; font-size: 18px; color: {{ $audit->action_type === 'stock_in' ? '#28a745' : '#dc3545' }};">
                                                {{ $audit->action_type === 'stock_in' ? '+' : '-' }}{{ $audit->quantity }}
                                            </div>
                                            <div style="font-size: 12px; color: #6c757d;">
                                                {{ $audit->supply->unit }}
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        
                        @if($auditTrails->hasPages())
                            {{ $auditTrails->appends(request()->query())->links('vendor.pagination.simple') }}
                        @endif
                    @else
                        <div class="empty-state">
                            <i class="fas fa-history"></i>
                            <h3>No audit records found</h3>
                            <p>No stock movements have been logged yet.</p>
                        </div>
                    @endif
                </div>

                <!-- Chart View -->
                <div id="chartView" class="chart-container" style="display: none;">
                    <div class="chart-grid">
                        <div class="chart-card">
                            <h3><i class="fas fa-boxes"></i> Recent Stock Updates</h3>
                            <canvas id="recentStockChart"></canvas>
                        </div>
                        
                        <div class="chart-card">
                            <h3><i class="fas fa-chart-pie"></i> Action Distribution</h3>
                            <canvas id="pieChart"></canvas>
                        </div>
                        
                        <div class="chart-card">
                            <h3><i class="fas fa-chart-bar"></i> Top Items by Activity</h3>
                            <canvas id="topItemsChart"></canvas>
                        </div>
                        
                        <div class="chart-card">
                            <h3><i class="fas fa-users"></i> Activity by User</h3>
                            <canvas id="userActivityChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>   
    </div>

    <script>
        const chartData = @json($chartData ?? []);

        $(document).ready(function() {
            $('.toggle-btn').on('click', function() {
                const view = $(this).data('view');
                $('.toggle-btn').removeClass('active');
                $(this).addClass('active');
                
                if (view === 'table') {
                    $('#tableView').show();
                    $('#chartView').hide();
                } else {
                    $('#tableView').hide();
                    $('#chartView').show();
                    initCharts();
                }
            });

            $('#dateFrom, #dateTo, #userFilter, #actionFilter').on('change', updateURL);
            $('#clearFilters').on('click', function() {
                window.location.href = '{{ route("client.stockcard.audit-trail") }}';
            });

            function updateURL() {
                const url = new URL(window.location.href);
                const params = {
                    date_from: $('#dateFrom').val(),
                    date_to: $('#dateTo').val(),
                    user_id: $('#userFilter').val(),
                    action_type: $('#actionFilter').val()
                };

                Object.keys(params).forEach(key => {
                    params[key] ? url.searchParams.set(key, params[key]) : url.searchParams.delete(key);
                });

                url.searchParams.delete('page'); // Reset to first page
                window.location.href = url.toString();
            }

            let chartsInitialized = false;
            
            function initCharts() {
                if (chartsInitialized || !chartData) return;
                chartsInitialized = true;

                new Chart(document.getElementById('recentStockChart'), {
                    type: 'bar',
                    data: {
                        labels: chartData.recentStock.labels,
                        datasets: [{
                            label: 'Latest Stock Movement',
                            data: chartData.recentStock.quantities,
                            backgroundColor: chartData.recentStock.colors,
                            borderWidth: 2,
                            borderColor: chartData.recentStock.borderColors
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { 
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const type = chartData.recentStock.types[context.dataIndex];
                                        return type === 'stock_in' ? 
                                            'Stock In: +' + context.parsed.y : 
                                            'Stock Out: -' + Math.abs(context.parsed.y);
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return Math.abs(value);
                                    }
                                }
                            }
                        }
                    }
                });

                new Chart(document.getElementById('pieChart'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Stock In', 'Stock Out'],
                        datasets: [{
                            data: [chartData.totals.stockIn, chartData.totals.stockOut],
                            backgroundColor: ['#28a745', '#dc3545']
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { position: 'bottom' } }
                    }
                });

                new Chart(document.getElementById('topItemsChart'), {
                    type: 'bar',
                    data: {
                        labels: chartData.topItems.labels,
                        datasets: [{
                            label: 'Transactions',
                            data: chartData.topItems.data,
                            backgroundColor: '#296218'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        indexAxis: 'y',
                        plugins: { legend: { display: false } }
                    }
                });

                new Chart(document.getElementById('userActivityChart'), {
                    type: 'bar',
                    data: {
                        labels: chartData.userActivity.labels,
                        datasets: [{
                            label: 'Activities',
                            data: chartData.userActivity.data,
                            backgroundColor: '#007bff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } }
                    }
                });
            }
        });
    </script>

    <style>
        .filter-row {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .view-toggle {
            display: flex;
            gap: 0;
            background: rgba(255,255,255,0.2);
            border-radius: 8px;
            padding: 3px;
        }

        .toggle-btn {
            padding: 8px 16px;
            border: none;
            background: transparent;
            cursor: pointer;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            color: rgba(255,255,255,0.8);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
        }

        .toggle-btn:hover {
            color: white;
        }

        .toggle-btn.active {
            background: white;
            color: #296218;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .form-control, .form-select {
            padding: 8px 12px;
            border: 2px solid rgba(255,255,255,0.3);
            border-radius: 6px;
            font-size: 13px;
            background: white;
            flex: 1;
            min-width: 120px;
        }

        .form-control:focus, .form-select:focus {
            outline: none;
            border-color: white;
        }

        .btn-clear {
            padding: 8px 12px;
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
        }

        .btn-clear:hover {
            background: #c82333;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .badge-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .badge i {
            font-size: 10px;
        }

        .chart-container {
            padding: 15px;
        }

        .chart-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }

        .chart-card {
            background: white;
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .chart-card h3 {
            margin: 0 0 15px 0;
            font-size: 15px;
            font-weight: 600;
            color: #333;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .chart-card h3 i {
            color: #296218;
        }

        .chart-card canvas {
            height: 250px !important;
        }

        @media (max-width: 1200px) {
            .chart-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .filter-row {
                flex-wrap: wrap;
            }

            .form-control, .form-select {
                min-width: calc(50% - 4px);
            }

            .supplies-header > div:first-child {
                flex-direction: column;
                align-items: flex-start !important;
                gap: 10px;
            }

            .view-toggle {
                width: 100%;
            }

            .toggle-btn {
                flex: 1;
                justify-content: center;
            }
        }
    </style>

    @include('layouts.core.footer')
</body>
</html>