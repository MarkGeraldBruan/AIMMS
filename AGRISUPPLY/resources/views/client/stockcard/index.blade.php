<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Card</title>
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
                <div class="supplies-header">
                    <h1 class="supplies-title">
                        <i class="fas fa-clipboard-list"></i>
                        Stock Card
                    </h1>
                    
                    <!-- Controls Row -->
                    <div class="controls-row">
                        <div class="search-filter-group">
                            <div class="search-box">
                                <i class="fas fa-search"></i>
                                <input type="text" placeholder="Search by items, categories, or ID"
                                       value="{{ request('search') }}" id="searchInput">
                            </div>
                        </div>
                        
                        <div class="action-buttons">
                            @if(auth()->user()->hasPermission('stock_in'))
                                <a href="{{ route('client.stockcard.stock-in') }}" class="btn btn-success">
                                    <i class="fas fa-arrow-down"></i>
                                    Stock In
                                </a>
                            @endif

                            @if(auth()->user()->hasPermission('stock_out'))
                                <a href="{{ route('client.stockcard.stock-out') }}" class="btn btn-danger">
                                    <i class="fas fa-arrow-up"></i>
                                    Stock Out
                                </a>
                            @endif

                            <a href="{{ route('client.stockcard.export.all.excel', request()->query()) }}" class="btn btn-primary">
                                <i class="fas fa-download"></i>
                                Export
                            </a>

                            @if(!auth()->user()->hasPermission('stock_in') && !auth()->user()->hasPermission('stock_out'))
                                <div class="no-permission-message">
                                    <i class="fas fa-lock"></i>
                                    <span>View Only Access</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Alerts -->
                @if(session('success'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ session('error') }}
                    </div>
                @endif
                
                <!-- Table Section -->
                <div class="supplies-table-container">
                    @if($supplies->count() > 0)
                        <table class="supplies-table">
                            <thead>
                                <tr>
                                    <th style="width: 5%; text-align: center;">
                                        <i class="fas fa-hashtag"></i>
                                    </th>
                                    <th style="width: 25%;">
                                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'id', 'sort_direction' => request('sort_direction') == 'asc' ? 'desc' : 'asc']) }}">
                                            <i class="fas fa-user" style="margin-right: 5px;"></i>Stock No.
                                            @if(request('sort_by') == 'id')
                                                <i class="fas fa-sort-{{ request('sort_direction') == 'asc' ? 'up' : 'down' }}"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th style="width: 55%;">
                                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'name', 'sort_direction' => request('sort_direction') == 'asc' ? 'desc' : 'asc']) }}">
                                            <i class="fas fa-info-circle" style="margin-right: 5px;"></i>Description
                                            @if(request('sort_by') == 'name')
                                                <i class="fas fa-sort-{{ request('sort_direction') == 'asc' ? 'up' : 'down' }}"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th style="width: 15%; text-align: center;">
                                        <i class="fas fa-cog" style="margin-right: 5px;"></i>Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($supplies as $supply)
                                    <tr>
                                        <td style="text-align: center;">
                                            <div style="font-weight: 600; color: #6c757d; font-size: 14px;">
                                                {{ $loop->iteration }}
                                            </div>
                                        </td>
                                        <td>
                                            <div style="font-weight: 600; color: #333; font-size: 16px;">
                                                #{{ str_pad($supply->id, 4, '0', STR_PAD_LEFT) }}
                                            </div>
                                            <div style="font-size: 12px; color: #6c757d; margin-top: 2px;">
                                                Item ID
                                            </div>
                                        </td>
                                        <td>
                                            <div style="margin-bottom: 8px;">
                                                <!-- Combined Description Format -->
                                                <div style="font-weight: 600; font-size: 16px; color: #333; margin-bottom: 4px; line-height: 1.4;">
                                                    {{ $supply->name }}@if($supply->description), {{ $supply->description }}@endif, {{ $supply->quantity }} {{ $supply->unit }}
                                                </div>
                                            
                                            </div>
                                        </td>
                                        <td style="text-align: center;">
                                            <div class="action-buttons-cell">
                                                <a href="{{ route('client.stockcard.show', $supply->id) }}" class="btn btn-primary btn-sm" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        
                        <!-- Pagination -->
                        @if($supplies->hasPages())
                            {{ $supplies->appends(request()->query())->links('vendor.pagination.simple') }}
                        @endif
                    @else
                        <div class="empty-state">
                            <i class="fas fa-clipboard-list"></i>
                            <h3>No stock items found</h3>
                            <p>No supplies available in the system.</p>
                            @if(auth()->user()->hasPermission('create'))
                                <a href="{{ route('supplies.create') }}" class="btn btn-success">
                                    <i class="fas fa-plus"></i>
                                    Add Supplies First
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>   
    </div>

    <script>
        $(document).ready(function() {
            // Setup CSRF token for AJAX
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Search functionality
            let searchTimeout;
            $('#searchInput').on('input', function() {
                clearTimeout(searchTimeout);
                const searchTerm = $(this).val();

                searchTimeout = setTimeout(() => {
                    const url = new URL(window.location.href);
                    if (searchTerm) {
                        url.searchParams.set('search', searchTerm);
                    } else {
                        url.searchParams.delete('search');
                    }
                    url.searchParams.delete('page'); // Reset to first page
                    window.location.href = url.toString();
                }, 500);
            });

            // Category filter
            $('#categoryFilter').on('change', function() {
                const category = $(this).val();
                const url = new URL(window.location.href);
                if (category) {
                    url.searchParams.set('category', category);
                } else {
                    url.searchParams.delete('category');
                }
                url.searchParams.delete('page'); // Reset to first page
                window.location.href = url.toString();
            });

            // Stock filter
            $('#stockFilter').on('change', function() {
                const lowStock = $(this).val();
                const url = new URL(window.location.href);
                if (lowStock) {
                    url.searchParams.set('low_stock', lowStock);
                } else {
                    url.searchParams.delete('low_stock');
                }
                url.searchParams.delete('page'); // Reset to first page
                window.location.href = url.toString();
            });
        });
    </script>

    <style>
        .no-permission-message {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: #f8f9fa;
            border: 2px solid #dee2e6;
            border-radius: 6px;
            color: #6c757d;
            font-size: 14px;
            font-weight: 600;
        }

        .no-permission-message i {
            font-size: 16px;
        }
    </style>

    @include('layouts.core.footer')
</body>
</html>
