<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Supplies</title>
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
                        <i class="fas fa-boxes"></i>
                        Manage Supplies
                    </h1>
                    
                    <!-- Controls Row -->
                    <div class="controls-row">
                        <div class="search-filter-group">
                            <div class="search-box">
                                <i class="fas fa-search"></i>
                                <input type="text" placeholder="Search by items, categories, or ID"
                                       value="{{ request('search') }}" id="searchInput">
                            </div>
                            
                            <div class="filter-dropdown">
                                <select id="categoryFilter">
                                    <option value="">All Categories</option>
                                    @foreach($categories ?? [] as $category)
                                        <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                            {{ $category }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="filter-dropdown">
                                <select id="stockFilter">
                                    <option value="">All Items</option>
                                    <option value="1" {{ request('low_stock') == '1' ? 'selected' : '' }}>Low Stock Only</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="action-buttons">
                            {{-- Only show Add button if user has create permission --}}
                            @if(auth()->user()->hasPermission('create'))
                                <a href="{{ route('supplies.create') }}" class="btn btn-success">
                                    <i class="fas fa-plus"></i>
                                    Add Items
                                </a>
                            @endif
                            @if(auth()->user()->hasPermission('read'))
                                <a href="{{ route('deleted-supplies.index') }}" class="btn btn-warning">
                                    <i class="fas fa-trash-restore"></i>
                                    Archive
                                </a>
                            @endif
                            {{-- Only show Export if user can read --}}
                            @if(auth()->user()->hasPermission('read'))
                                <a href="{{ route('supplies.export', request()->query()) }}" class="btn btn-primary">
                                    <i class="fas fa-download"></i>
                                    Export
                                </a>
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
                
                <!-- Permission Warning -->
                @if(!auth()->user()->hasPermission('read'))
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        You do not have permission to view supplies. Please contact an administrator.
                    </div>
                @else
                    <!-- Table Section -->
                    <div class="supplies-table-container">
                        @if($supplies->count() > 0)
                            <table class="supplies-table">
                                <thead>
                                    <tr>
                                        <th>
                                            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'id', 'sort_direction' => request('sort_direction') == 'asc' ? 'desc' : 'asc']) }}">
                                                ID
                                                @if(request('sort_by') == 'id')
                                                    <i class="fas fa-sort-{{ request('sort_direction') == 'asc' ? 'up' : 'down' }}"></i>
                                                @endif
                                            </a>
                                        </th>
                                        <th>
                                            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'name', 'sort_direction' => request('sort_direction') == 'asc' ? 'desc' : 'asc']) }}">
                                                Items
                                                @if(request('sort_by') == 'name')
                                                    <i class="fas fa-sort-{{ request('sort_direction') == 'asc' ? 'up' : 'down' }}"></i>
                                                @endif
                                            </a>
                                        </th>
                                        <th>
                                            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'quantity', 'sort_direction' => request('sort_direction') == 'asc' ? 'desc' : 'asc']) }}">
                                                Quantity
                                                @if(request('sort_by') == 'quantity')
                                                    <i class="fas fa-sort-{{ request('sort_direction') == 'asc' ? 'up' : 'down' }}"></i>
                                                @endif
                                            </a>
                                        </th>
                                        <th>
                                            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'purchase_date', 'sort_direction' => request('sort_direction') == 'asc' ? 'desc' : 'asc']) }}">
                                                Date
                                                @if(request('sort_by') == 'purchase_date')
                                                    <i class="fas fa-sort-{{ request('sort_direction') == 'asc' ? 'up' : 'down' }}"></i>
                                                @endif
                                            </a>
                                        </th>
                                        <th>
                                            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'category', 'sort_direction' => request('sort_direction') == 'asc' ? 'desc' : 'asc']) }}">
                                                Categories
                                                @if(request('sort_by') == 'category')
                                                    <i class="fas fa-sort-{{ request('sort_direction') == 'asc' ? 'up' : 'down' }}"></i>
                                                @endif
                                            </a>
                                        </th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($supplies as $supply)
                                        <tr>
                                            <td>
                                                <div style="font-weight: 600; color: #6c757d; font-size: 14px;">
                                                    #{{ str_pad($supply->id, 4, '0', STR_PAD_LEFT) }}
                                                </div>
                                            </td>
                                            <td>
                                                <div style="font-weight: 600; margin-bottom: 4px;">{{ $supply->name }}</div>
                                                @if($supply->description)
                                                    <div style="font-size: 12px; color: #6c757d;">{{ Str::limit($supply->description, 50) }}</div>
                                                @endif
                                                @if($supply->supplier)
                                                    <div style="font-size: 11px; color: #007bff;">{{ $supply->supplier }}</div>
                                                @endif
                                            </td>
                                            <td>
                                                <div style="font-weight: 600;">{{ number_format($supply->quantity) }} {{ $supply->unit }}</div>
                                                <div style="font-size: 12px; color: #6c757d;">₱{{ number_format($supply->unit_price, 2) }} each</div>
                                                @if($supply->isLowStock())
                                                    <span class="status-badge status-low-stock">
                                                        <i class="fas fa-exclamation-triangle"></i>
                                                        Low Stock
                                                    </span>
                                                @else
                                                    <span class="status-badge status-in-stock">
                                                        <i class="fas fa-check"></i>
                                                        In Stock
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $supply->formatted_purchase_date }}
                                            </td>
                                            <td>
                                                @if($supply->category)
                                                    <span class="status-badge" style="background: #e3f2fd; color: #1565c0;">
                                                        {{ $supply->category }}
                                                    </span>
                                                @else
                                                    <span style="color: #6c757d;">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="action-buttons-cell">
                                                    {{-- View button - requires read permission --}}
                                                    @if(auth()->user()->hasPermission('read'))
                                                        <a href="{{ route('supplies.show', $supply) }}" class="btn btn-primary btn-sm" title="View">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    @endif

                                                    {{-- Edit button - requires update permission --}}
                                                    @if(auth()->user()->hasPermission('update'))
                                                        <a href="{{ route('supplies.edit', $supply) }}" class="btn btn-warning btn-sm" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    @endif

                                                    {{-- Delete button - requires delete permission --}}
                                                    @if(auth()->user()->hasPermission('delete'))
                                                        <form method="POST" action="{{ route('supplies.destroy', $supply) }}" style="display: inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm" title="Delete"
                                                                    onclick="return confirm('Are you sure you want to delete this item?')">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    @else
                                                        {{-- Show locked icon if no delete permission --}}
                                                        <button class="btn btn-sm"
                                                                style="background: #6c757d; color: white; cursor: not-allowed;"
                                                                title="No delete permission" disabled>
                                                            <i class="fas fa-lock"></i>
                                                        </button>
                                                    @endif
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
                                <i class="fas fa-boxes"></i>
                                <h3>No supplies found</h3>
                                <p>Get started by adding your first supply item.</p>
                                @if(auth()->user()->hasPermission('create'))
                                    <a href="{{ route('supplies.create') }}" class="btn btn-success">
                                        <i class="fas fa-plus"></i>
                                        Add Your First Item
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>   
    </div>

    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

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

    @include('layouts.core.footer')
</body>
</html>