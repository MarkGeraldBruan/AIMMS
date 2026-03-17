<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Equipment</title>
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
                        <i class="fas fa-tools"></i>
                        Manage Equipment
                    </h1>
                    
                    <!-- Controls Row -->
                    <div class="controls-row">
                        <div class="search-filter-group">
                            <div class="search-box">
                                <i class="fas fa-search"></i>
                                <input type="text" placeholder="Search by article, description, or ID"
                                       value="{{ request('search') }}" id="searchInput">
                            </div>
                            
                            <div class="filter-dropdown">
                                <select id="conditionFilter">
                                    <option value="">All Conditions</option>
                                    @foreach($conditions ?? [] as $condition)
                                        <option value="{{ $condition }}" {{ request('condition') == $condition ? 'selected' : '' }}>
                                            {{ $condition }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="action-buttons">
                            @if(auth()->user()->hasPermission('create'))
                                <a href="{{ route('equipment.create') }}" class="btn btn-success">
                                    <i class="fas fa-plus"></i>
                                    Add Equipment
                                </a>
                            @endif
                            
                            @if(auth()->user()->hasPermission('read'))
                                <a href="{{ route('equipment.export', request()->query()) }}" class="btn btn-primary">
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

                @if(session('error'))
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ session('error') }}
                    </div>
                @endif
                
                <!-- Permission Warning -->
                @if(!auth()->user()->hasPermission('read'))
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        You do not have permission to view equipment. Please contact an administrator.
                    </div>
                @else
                    <!-- Table Section -->
                    <div class="supplies-table-container">
                        @if($equipment->count() > 0)
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
                                            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'article', 'sort_direction' => request('sort_direction') == 'asc' ? 'desc' : 'asc']) }}">
                                                Article
                                                @if(request('sort_by') == 'article')
                                                    <i class="fas fa-sort-{{ request('sort_direction') == 'asc' ? 'up' : 'down' }}"></i>
                                                @endif
                                            </a>
                                        </th>
                                        <th>Unit & Value</th>
                                        <th>
                                            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'condition', 'sort_direction' => request('sort_direction') == 'asc' ? 'desc' : 'asc']) }}">
                                                Condition
                                                @if(request('sort_by') == 'condition')
                                                    <i class="fas fa-sort-{{ request('sort_direction') == 'asc' ? 'up' : 'down' }}"></i>
                                                @endif
                                            </a>
                                        </th>
                                        <th>
                                            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'location', 'sort_direction' => request('sort_direction') == 'asc' ? 'desc' : 'asc']) }}">
                                                Responsibility Center
                                                @if(request('sort_by') == 'location')
                                                    <i class="fas fa-sort-{{ request('sort_direction') == 'asc' ? 'up' : 'down' }}"></i>
                                                @endif
                                            </a>
                                        </th>
                                        <th>
                                            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'acquisition_date', 'sort_direction' => request('sort_direction') == 'asc' ? 'desc' : 'asc']) }}">
                                                Acquisition Date
                                                @if(request('sort_by') == 'acquisition_date')
                                                    <i class="fas fa-sort-{{ request('sort_direction') == 'asc' ? 'up' : 'down' }}"></i>
                                                @endif
                                            </a>
                                        </th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($equipment as $item)
                                        <tr>
                                            <td>
                                                <div style="font-weight: 600; color: #6c757d; font-size: 14px;">
                                                    #{{ str_pad($item->id, 4, '0', STR_PAD_LEFT) }}
                                                </div>
                                            </td>
                                            <td>
                                                <div style="font-weight: 600; margin-bottom: 4px;">{{ $item->article }}</div>
                                                @if($item->description)
                                                    <div style="font-size: 12px; color: #6c757d;">{{ Str::limit($item->description, 50) }}</div>
                                                @endif
                                            </td>
                                            <td>
                                                <div style="font-weight: 600;">{{ $item->unit_of_measurement }}</div>
                                                <div style="font-size: 12px; color: #28a745; font-weight: 600;">
                                                    ₱{{ number_format($item->unit_value, 2) }}
                                                </div>
                                            </td>
                                            <td>
                                                @if($item->condition === 'Serviceable')
                                                    <span class="status-badge" style="background: #d4edda; color: #155724; border: 1px solid #a3d9a5;">
                                                        <i class="fas fa-check-circle"></i>
                                                        Serviceable
                                                    </span>
                                                @else
                                                    <span class="status-badge" style="background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb;">
                                                        <i class="fas fa-times-circle"></i>
                                                        Unserviceable
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($item->location)
                                                    <div style="font-weight: 600; color: #495057;">
                                                        {{ $item->location }}
                                                    </div>
                                                @else
                                                    <span style="color: #6c757d; font-size: 12px;">Not specified</span>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $item->formatted_acquisition_date }}
                                            </td>
                                            <td>
                                                <div class="action-buttons-cell">
                                                    @if(auth()->user()->hasPermission('read'))
                                                        <a href="{{ route('equipment.show', $item) }}" class="btn btn-primary btn-sm" title="View">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    @endif

                                                    @if(auth()->user()->hasPermission('update'))
                                                        <a href="{{ route('equipment.edit', $item) }}" class="btn btn-warning btn-sm" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    @endif

                                                    @if(auth()->user()->hasPermission('delete'))
                                                        <form method="POST" action="{{ route('equipment.destroy', $item) }}" style="display: inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm" title="Delete"
                                                                    onclick="return confirm('Are you sure you want to delete this equipment?')">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    @else
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
                        @if($equipment->hasPages())
                            {{ $equipment->appends(request()->query())->links('vendor.pagination.simple') }}
                        @endif
                        @else
                            <div class="empty-state">
                                <i class="fas fa-tools"></i>
                                <h3>No equipment found</h3>
                                <p>Get started by adding your first equipment item.</p>
                                @if(auth()->user()->hasPermission('create'))
                                    <a href="{{ route('equipment.create') }}" class="btn btn-success">
                                        <i class="fas fa-plus"></i>
                                        Add Your First Equipment
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

            $('#conditionFilter').on('change', function() {
                const condition = $(this).val();
                const url = new URL(window.location.href);
                if (condition) {
                    url.searchParams.set('condition', condition);
                } else {
                    url.searchParams.delete('condition');
                }
                url.searchParams.delete('page'); // Reset to first page
                window.location.href = url.toString();
            });
        });
    </script>

    @include('layouts.core.footer')
</body>
</html>
