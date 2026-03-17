<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>AGRISUPPLY - Manage Users</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/users.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container">
        @include('layouts.core.sidebar')
        <div class="details">
            @include('layouts.core.header')
            
            <div class="users-container">
                <!-- Header Section -->
                <div class="users-header">
                    <h1 class="users-title">
                        <i class="fas fa-users"></i>
                        Manage Users
                    </h1>
                    
                    <!-- Controls Row -->
                    <div class="controls-row">
                        <div class="search-filter-group">
                            <div class="search-box">
                                <i class="fas fa-search"></i>
                                <input type="text" placeholder="Search by name or email" 
                                       id="userSearch">
                            </div>
                            
                            <div class="filter-dropdown">
                                <select id="roleFilter">
                                    <option value="">All Roles</option>
                                    <option value="admin">Admin</option>
                                    <option value="user">User</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="action-buttons">
                            <a href="{{ url('client/users/create') }}" class="btn btn-success">
                                <i class="fas fa-plus"></i>
                                Add User
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
                
                <!-- Table Section -->
                <div class="users-table-container">
                    @if(count($users ?? []) > 0)
                        <table class="users-table">
                            <thead>
                                <tr>
                                    <th style="width: 5%; text-align: center;">
                                        <i class="fas fa-hashtag"></i>
                                    </th>
                                    <th style="width: 20%;">
                                        <i class="fas fa-user" style="margin-right: 5px;"></i>User
                                    </th>
                                    <th style="width: 20%;">
                                        <i class="fas fa-envelope" style="margin-right: 5px;"></i>Email
                                    </th>
                                    <th style="width: 10%; text-align: center;">
                                        <i class="fas fa-user-tag" style="margin-right: 5px;"></i>Role
                                    </th>
                                    <th style="width: 30%; text-align: center;">
                                        <i class="fas fa-shield-alt" style="margin-right: 5px;"></i>Permissions
                                    </th>
                                    <th style="width: 15%; text-align: center;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $key => $userItem)
                                    <tr class="user-row" data-role="{{ strtolower($userItem->role ?? 'user') }}">
                                        <td style="text-align: center; font-weight: 600; color: #495057;">
                                            {{ $key + 1 }}
                                        </td>
                                        <td>
                                            <div style="display: flex; align-items: center;">
                                                <div style="width: 32px; height: 32px; background: #296218; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 10px; flex-shrink: 0;">
                                                    <i class="fas fa-user" style="color: white; font-size: 14px;"></i>
                                                </div>
                                                <div style="min-width: 0; flex: 1;">
                                                    <div class="user-name" style="font-weight: 600; color: #333; font-size: 14px; margin-bottom: 2px;">{{ $userItem->name }}</div>
                                                    <div style="font-size: 11px; color: #6c757d;">
                                                        <i class="fas fa-calendar-alt" style="margin-right: 4px;"></i>
                                                        {{ $userItem->created_date }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="user-email" style="color: #495057;">
                                            <div style="display: flex; align-items: center;">
                                                <i class="fas fa-envelope" style="color: #666; margin-right: 8px;"></i>
                                                {{ $userItem->email }}
                                            </div>
                                        </td>
                                        <td style="text-align: center;">
                                            <span class="role-badge {{ ($userItem->role ?? 'user') == 'admin' ? 'role-admin' : 'role-user' }}">
                                                <i class="fas fa-{{ ($userItem->role ?? 'user') == 'admin' ? 'user-shield' : 'user' }}"></i>
                                                {{ ucfirst($userItem->role ?? 'user') }}
                                            </span>
                                        </td>
                                        <td style="text-align: center;">
                                            @if(($userItem->role ?? 'user') == 'admin')
                                                <span class="permission-badge permission-full">
                                                    <i class="fas fa-crown"></i>
                                                    Full Access
                                                </span>
                                            @else
                                                <div class="permissions-display" style="justify-content: center;">
                                                    <span class="perm-badge {{ $userItem->can_create ? 'perm-create' : 'perm-disabled' }}" 
                                                          title="{{ $userItem->can_create ? 'Can Create' : 'Cannot Create' }}">
                                                        C
                                                    </span>
                                                    <span class="perm-badge {{ $userItem->can_read ? 'perm-read' : 'perm-disabled' }}" 
                                                          title="{{ $userItem->can_read ? 'Can Read' : 'Cannot Read' }}">
                                                        R
                                                    </span>
                                                    <span class="perm-badge {{ $userItem->can_update ? 'perm-update' : 'perm-disabled' }}" 
                                                          title="{{ $userItem->can_update ? 'Can Update' : 'Cannot Update' }}">
                                                        U
                                                    </span>
                                                    <span class="perm-badge {{ $userItem->can_delete ? 'perm-delete' : 'perm-disabled' }}" 
                                                          title="{{ $userItem->can_delete ? 'Can Delete' : 'Cannot Delete' }}">
                                                        D
                                                    </span>
                                                </div>
                                            @endif
                                        </td>
                                        <td style="text-align: center;">
                                            <div class="action-buttons-cell">
                                                <a href="{{ url('client/users', $userItem->id) }}/edit" 
                                                   class="btn btn-warning btn-sm" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @if($userItem->id !== auth()->user()->id)
                                                    <button onclick="removeUser({{ $userItem->id }})" 
                                                            class="btn btn-danger btn-sm" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @else
                                                    <span class="btn btn-sm" 
                                                          style="background: #6c757d; color: white; cursor: not-allowed;" 
                                                          title="Cannot delete your own account">
                                                        <i class="fas fa-lock"></i>
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-users-slash"></i>
                            <h3>No users found</h3>
                            <p>Get started by adding your first user to the system.</p>
                            <a href="{{ url('client/users/create') }}" class="btn btn-success">
                                <i class="fas fa-plus"></i>
                                Add Your First User
                            </a>
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
            $('#userSearch').on('input', function() {
                clearTimeout(searchTimeout);
                
                searchTimeout = setTimeout(() => {
                    filterTable();
                }, 300);
            });

            // Role filter
            $('#roleFilter').on('change', function() {
                filterTable();
            });

            function filterTable() {
                const searchTerm = $('#userSearch').val().toLowerCase().trim();
                const roleFilter = $('#roleFilter').val().toLowerCase().trim();
                
                $('.user-row').each(function() {
                    const row = $(this);
                    const name = row.find('.user-name').text().toLowerCase().trim();
                    const email = row.find('.user-email').text().toLowerCase().trim();
                    const userRole = row.data('role');
                    
                    let showRow = true;
                    
                    if (searchTerm && !name.includes(searchTerm) && !email.includes(searchTerm)) {
                        showRow = false;
                    }
                    
                    if (roleFilter && userRole !== roleFilter) {
                        showRow = false;
                    }
                    
                    if (showRow) {
                        row.show();
                    } else {
                        row.hide();
                    }
                });

                updateNoResultsMessage();
            }

            function updateNoResultsMessage() {
                const visibleRows = $('.user-row:visible').length;
                const tableContainer = $('.users-table-container');
                
                $('.no-results-message').remove();
                
                if (visibleRows === 0 && $('.user-row').length > 0) {
                    tableContainer.append(`
                        <div class="no-results-message" style="text-align: center; padding: 40px; color: #6c757d;">
                            <i class="fas fa-search" style="font-size: 48px; margin-bottom: 16px; opacity: 0.5;"></i>
                            <h3>No users match your search</h3>
                            <p>Try adjusting your search terms or filters.</p>
                        </div>
                    `);
                    $('.users-table').hide();
                } else {
                    $('.users-table').show();
                }
            }
        });

        function removeUser(id) {
            if(confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
                $.ajax({
                    type: "DELETE",
                    url: "{{ url('client/users') }}/" + id,
                    dataType: "json",
                    beforeSend: function() {
                        $('body').append('<div class="loading-overlay" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 9999; display: flex; align-items: center; justify-content: center; color: white; font-size: 18px;"><i class="fas fa-spinner fa-spin" style="margin-right: 10px;"></i> Deleting...</div>');
                    },
                    success: function(response) {
                        $('.loading-overlay').remove();
                        location.reload();
                    },
                    error: function(xhr, status, error) {
                        $('.loading-overlay').remove();
                        alert('Error deleting user. Please try again.');
                        console.error('Error:', error);
                    }
                });
            }
        }
    </script>

    @include('layouts.core.footer')
</body>
</html>