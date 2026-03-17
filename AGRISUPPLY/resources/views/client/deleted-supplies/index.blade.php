
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deleted Items History</title>
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
                <div class="supplies-header">
                    <h1 class="supplies-title">
                        <i class="fas fa-trash-restore"></i>
                        Deleted Items History
                    </h1>
                    
                    <div class="controls-row">
                        <div class="search-filter-group">
                            <div class="search-box">
                                <i class="fas fa-search"></i>
                                <input type="text" placeholder="Search deleted items" 
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
                        </div>
                        
                        <div class="action-buttons">
                            <a href="{{ route('supplies.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i>
                                Back to Supplies
                            </a>
                        </div>
                    </div>
                </div>
                
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
                
                <div class="supplies-table-container">
                    @if($deletedItems->count() > 0)
                        <table class="supplies-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Item Details</th>
                                    <th>Quantity</th>
                                    <th>Deleted By</th>
                                    <th>Deleted On</th>
                                    <th style="text-align: center;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($deletedItems as $item)
                                    <tr>
                                        <td>#{{ str_pad($item->supply_id, 4, '0', STR_PAD_LEFT) }}</td>
                                        <td>
                                            <div style="font-weight: 600;">{{ $item->name }}</div>
                                            @if($item->category)
                                                <span class="status-badge" style="background: #e3f2fd; color: #1565c0;">
                                                    {{ $item->category }}
                                                </span>
                                            @endif
                                        </td>
                                        <td>{{ number_format($item->quantity) }} {{ $item->unit }}</td>
                                        <td>{{ $item->user->name }}</td>
                                        <td>{{ $item->created_at->format('M d, Y h:i A') }}</td>
                                        <td>
                                            <div class="action-buttons-cell" style="justify-content: center;">
                                                @if(auth()->user()->hasPermission('create'))
                                                    <form method="POST" action="{{ route('deleted-supplies.restore', $item) }}" style="display: inline;">
                                                        @csrf
                                                        <button type="submit" class="btn btn-success btn-sm" title="Restore"
                                                                onclick="return confirm('Restore this item?')">
                                                            <i class="fas fa-undo"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                                
                                                @if(auth()->user()->hasPermission('delete'))
                                                    <form method="POST" action="{{ route('deleted-supplies.permanent-delete', $item) }}" style="display: inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm" title="Permanent Delete"
                                                                onclick="return confirm('⚠️ PERMANENTLY delete? This CANNOT be undone!')">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        
                        @if($deletedItems->hasPages())
                            <div class="pagination">
                                {{ $deletedItems->appends(request()->query())->links() }}
                            </div>
                        @endif
                    @else
                        <div class="empty-state">
                            <i class="fas fa-trash-restore"></i>
                            <h3>No deleted items found</h3>
                            <p>There are no deleted items in the history.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>   
    </div>

    <script>
        $(document).ready(function() {
            let searchTimeout;
            $('#searchInput').on('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    const url = new URL(window.location.href);
                    const search = $(this).val();
                    search ? url.searchParams.set('search', search) : url.searchParams.delete('search');
                    window.location.href = url.toString();
                }, 500);
            });

            $('#categoryFilter').on('change', function() {
                const url = new URL(window.location.href);
                const category = $(this).val();
                category ? url.searchParams.set('category', category) : url.searchParams.delete('category');
                window.location.href = url.toString();
            });
        });
    </script>

    @include('layouts.core.footer')
</body>
</html>
