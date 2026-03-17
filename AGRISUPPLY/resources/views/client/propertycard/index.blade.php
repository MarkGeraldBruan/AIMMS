<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Card</title>
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
                        <i class="fas fa-file-alt"></i>
                        Property Card
                    </h1>
                    
                    <!-- Controls Row -->
                    <div class="controls-row">
                        <div class="search-filter-group">
                            <div class="search-box">
                                <i class="fas fa-search"></i>
                                <input type="text" placeholder="Search by article, description, property number, or ID"
                                       value="{{ request('search') }}" id="searchInput">
                            </div>

                            <div class="filter-dropdown">
                                <select id="conditionFilter">
                                    <option value="">All Conditions</option>
                                    <option value="Serviceable" {{ request('condition') == 'Serviceable' ? 'selected' : '' }}>
                                        Serviceable
                                    </option>
                                    <option value="Unserviceable" {{ request('condition') == 'Unserviceable' ? 'selected' : '' }}>
                                        Unserviceable
                                    </option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="action-buttons">
                            <a href="{{ route('equipment.export', request()->query()) }}" class="btn btn-primary">
                                <i class="fas fa-download"></i>
                                Export
                            </a>
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
                    @if($equipment->count() > 0)
                        <table class="supplies-table">
                            <thead>
                                <tr>
                                    <th style="width: 8%; text-align: center;">
                                        <i class="fas fa-hashtag"></i>
                                    </th>
                                    <th style="width: 22%;">
                                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'id', 'sort_direction' => request('sort_direction') == 'asc' ? 'desc' : 'asc']) }}">
                                            <i class="fas fa-barcode" style="margin-right: 5px;"></i>ID
                                            @if(request('sort_by') == 'id')
                                                <i class="fas fa-sort-{{ request('sort_direction') == 'asc' ? 'up' : 'down' }}"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th style="width: 38%;">
                                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'article', 'sort_direction' => request('sort_direction') == 'asc' ? 'desc' : 'asc']) }}">
                                            <i class="fas fa-info-circle" style="margin-right: 5px;"></i>Description
                                            @if(request('sort_by') == 'article')
                                                <i class="fas fa-sort-{{ request('sort_direction') == 'asc' ? 'up' : 'down' }}"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th style="width: 20%; text-align: center;">
                                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'condition', 'sort_direction' => request('sort_direction') == 'asc' ? 'desc' : 'asc']) }}" style="justify-content: center;">
                                            <i class="fas fa-comment-alt" style="margin-right: 5px;"></i>Remarks
                                            @if(request('sort_by') == 'condition')
                                                <i class="fas fa-sort-{{ request('sort_direction') == 'asc' ? 'up' : 'down' }}"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th style="width: 12%; text-align: center;">
                                        <i class="fas fa-cog" style="margin-right: 5px;"></i>Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($equipment as $item)
                                    <tr>
                                        <td style="text-align: center;">
                                            <div style="font-weight: 600; color: #6c757d; font-size: 14px;">
                                                {{ $loop->iteration }}
                                            </div>
                                        </td>
                                        <td>
                                            <div style="font-weight: 600; color: #333; font-size: 16px;">
                                                #{{ str_pad($item->id, 4, '0', STR_PAD_LEFT) }}
                                            </div>
                                            <div style="font-size: 12px; color: #6c757d; margin-top: 2px;">
                                                {{ $item->property_number }}
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <div style="font-weight: 600; font-size: 16px; color: #333; line-height: 1.4;">
                                                    {{ $item->article }}@if($item->description), {{ $item->description }}@endif
                                                </div>
                                            </div>
                                        </td>
                                        <td style="text-align: center; vertical-align: middle;">
                                            <span class="status-badge {{ $item->condition === 'Serviceable' ? 'status-in-stock' : 'status-low-stock' }}">
                                                @if($item->condition === 'Serviceable')
                                                    <i class="fas fa-check-circle"></i>
                                                @else
                                                    <i class="fas fa-times-circle"></i>
                                                @endif
                                                {{ $item->condition }}
                                            </span>
                                        </td>
                                        <td style="text-align: center; vertical-align: middle;">
                                            <div class="action-buttons-cell">
                                                <a href="{{ route('client.propertycard.show', $item->id) }}" class="btn btn-primary btn-sm" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
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
                            <i class="fas fa-file-alt"></i>
                            <h3>No property items found</h3>
                            <p>No equipment available in the system. Please add equipment first.</p>
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

            // Condition filter
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
