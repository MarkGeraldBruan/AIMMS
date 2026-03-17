<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcement Management</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/announcement.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <div class="container">
        @include('layouts.core.sidebar')
        <div class="details">
            @include('layouts.core.header')

            <div class="announcement-container">
                <!-- Header Section -->
                <div class="announcement-header-section">
                    <h1 class="announcement-title">
                        <i class="fas fa-bullhorn"></i>
                        Announcement
                    </h1>
                    
                    <!-- Controls Row -->
                    <div class="controls-row">
                        <div class="search-filter-group">
                            <div class="search-box">
                                <i class="fas fa-search"></i>
                                <input type="text" placeholder="Search announcements..." 
                                value="{{ request('search') }}" id="searchInput">
                            </div>
                        </div>
                        <div class="action-buttons">
                            <a href="{{ route('client.announcement.create') }}" class="btn btn-success">
                                <i class="fas fa-plus"></i>
                                Add New Event
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

                <!-- Announcements Table -->
                <div class="announcement-table-container">
                    <table class="announcement-table">
                        <thead>
                            <tr>
                                <th style="width: 50px;"><input type="checkbox" id="selectAll"></th>
                                <th>Title</th>
                                <th style="width: 120px;">Status</th>
                                <th style="width: 160px;">Date Added</th>
                                <th style="width: 160px;">Last Updated</th>
                                <th style="width: 140px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($announcements as $announcement)
                                <tr>
                                    <td><input type="checkbox" class="row-checkbox" data-id="{{ $announcement->id }}"></td>
                                    <td><strong>{{ $announcement->title }}</strong></td>
                                    <td>
                                        <span class="status-badge {{ $announcement->status === 'published' ? 'status-published' : 'status-draft' }}">
                                            <i class="fas fa-circle" style="font-size: 8px;"></i>
                                            {{ ucfirst($announcement->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $announcement->created_at->format('m/d/Y g:i A') }}</td>
                                    <td>{{ $announcement->updated_at->format('m/d/Y g:i A') }}</td>
                                    <td>
                                        <div class="action-buttons-cell">
                                            <a href="{{ route('client.announcement.show', $announcement->id) }}" 
                                               class="btn btn-primary btn-sm" 
                                               title="View"
                                               style="background: #17a2b8;">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('client.announcement.edit', $announcement->id) }}" 
                                               class="btn btn-warning btn-sm" 
                                               title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-danger btn-sm" 
                                                    title="Delete"
                                                    onclick="confirmDelete({{ $announcement->id }}, '{{ addslashes($announcement->title) }}')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6">
                                        <div class="empty-state">
                                            <i class="fas fa-bullhorn"></i>
                                            <h3>No Announcements Found</h3>
                                            <p>Start by creating your first announcement</p>
                                            <a href="{{ route('client.announcement.create') }}" class="btn btn-success">
                                                <i class="fas fa-plus"></i>
                                                Create Announcement
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($announcements->hasPages())
                    <div class="pagination">
                        {{ $announcements->links() }}
                    </div>
                @endif

                <!-- Bulk Actions Bar -->
                <div id="bulkActionsBar" style="display: none; position: fixed; bottom: 30px; left: 50%; transform: translateX(-50%); background: white; padding: 15px 30px; border-radius: 12px; box-shadow: 0 8px 32px rgba(0,0,0,0.2); z-index: 1000;">
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <span id="selectedCount" style="font-weight: 600; color: #495057;">0 selected</span>
                        <button type="button" class="btn btn-success btn-sm" onclick="confirmBulkPublish()">
                            <i class="fas fa-check"></i> Publish
                        </button>
                        <button type="button" class="btn btn-danger btn-sm" onclick="confirmBulkDelete()">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="confirm-modal">
        <div class="confirm-modal-content">
            <div class="confirm-modal-header">
                <i class="fas fa-exclamation-triangle"></i>
                <h3>Confirm Delete</h3>
            </div>
            <div class="confirm-modal-body">
                <p>Are you sure you want to delete this announcement?</p>
                <p class="announcement-name" id="deleteAnnouncementName"></p>
                <p class="warning-text">This action cannot be undone!</p>
            </div>
            <div class="confirm-modal-footer">
                <button class="btn btn-secondary" onclick="closeDeleteModal()">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button class="btn btn-danger" onclick="proceedDelete()">
                    <i class="fas fa-trash"></i> Delete
                </button>
            </div>
        </div>
    </div>

    <!-- Bulk Publish Confirmation Modal -->
    <div id="publishModal" class="confirm-modal">
        <div class="confirm-modal-content">
            <div class="confirm-modal-header">
                <i class="fas fa-check-circle"></i>
                <h3>Confirm Publish</h3>
            </div>
            <div class="confirm-modal-body">
                <p>Are you sure you want to publish <strong id="publishCount">0</strong> announcement(s)?</p>
                <p class="info-text">Published announcements will be visible to all users on the dashboard.</p>
            </div>
            <div class="confirm-modal-footer">
                <button class="btn btn-secondary" onclick="closePublishModal()">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button class="btn btn-success" onclick="proceedPublish()">
                    <i class="fas fa-check"></i> Publish
                </button>
            </div>
        </div>
    </div>

    <!-- Bulk Delete Confirmation Modal -->
    <div id="bulkDeleteModal" class="confirm-modal">
        <div class="confirm-modal-content">
            <div class="confirm-modal-header">
                <i class="fas fa-exclamation-triangle"></i>
                <h3>Confirm Bulk Delete</h3>
            </div>
            <div class="confirm-modal-body">
                <p>Are you sure you want to delete <strong id="bulkDeleteCount">0</strong> announcement(s)?</p>
                <p class="warning-text">This action cannot be undone!</p>
            </div>
            <div class="confirm-modal-footer">
                <button class="btn btn-secondary" onclick="closeBulkDeleteModal()">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button class="btn btn-danger" onclick="proceedBulkDelete()">
                    <i class="fas fa-trash"></i> Delete All
                </button>
            </div>
        </div>
    </div>

    <!-- Hidden form for delete action -->
    <form id="deleteForm" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    <style>
    /* Confirmation Modal Styles */
    .confirm-modal {
        display: none;
        position: fixed;
        z-index: 10000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.6);
        animation: fadeIn 0.3s ease;
    }

    .confirm-modal.active {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .confirm-modal-content {
        background-color: white;
        border-radius: 15px;
        width: 90%;
        max-width: 500px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        animation: slideUp 0.3s ease;
        overflow: hidden;
    }

    .confirm-modal-header {
        background: linear-gradient(135deg, #296218 0%, #1e4612 100%);
        color: white;
        padding: 25px;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .confirm-modal-header i {
        font-size: 32px;
    }

    .confirm-modal-header h3 {
        margin: 0;
        font-size: 22px;
    }

    #deleteModal .confirm-modal-header {
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    }

    #bulkDeleteModal .confirm-modal-header {
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    }

    .confirm-modal-body {
        padding: 30px;
    }

    .confirm-modal-body p {
        margin: 0 0 15px 0;
        color: #495057;
        font-size: 16px;
        line-height: 1.6;
    }

    .confirm-modal-body p:last-child {
        margin-bottom: 0;
    }

    .announcement-name {
        background: #f8f9fa;
        padding: 12px 15px;
        border-radius: 8px;
        font-weight: 600;
        color: #296218;
        border-left: 4px solid #296218;
    }

    .warning-text {
        color: #dc3545;
        font-weight: 600;
        font-size: 14px !important;
    }

    .info-text {
        color: #296218;
        font-size: 14px !important;
        font-style: italic;
    }

    .confirm-modal-footer {
        padding: 20px 30px;
        background: #f8f9fa;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        border-top: 1px solid #e9ecef;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @keyframes slideUp {
        from {
            transform: translateY(50px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    @media (max-width: 768px) {
        .confirm-modal-content {
            width: 95%;
        }
        
        .confirm-modal-header {
            padding: 20px;
        }
        
        .confirm-modal-body {
            padding: 20px;
        }
        
        .confirm-modal-footer {
            padding: 15px 20px;
            flex-direction: column-reverse;
        }
        
        .confirm-modal-footer .btn {
            width: 100%;
            justify-content: center;
        }
    }
    </style>

    <script>
        // Search functionality
        const searchInput = document.getElementById('searchInput');
        let searchTimeout;

        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                const url = new URL(window.location.href);
                if (this.value) {
                    url.searchParams.set('search', this.value);
                } else {
                    url.searchParams.delete('search');
                }
                window.location.href = url.toString();
            }, 500);
        });

        // Select all functionality
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.row-checkbox');
        const bulkActionsBar = document.getElementById('bulkActionsBar');
        const selectedCount = document.getElementById('selectedCount');

        selectAll.addEventListener('change', function() {
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateBulkActions();
        });

        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateBulkActions);
        });

        function updateBulkActions() {
            const selectedCheckboxes = document.querySelectorAll('.row-checkbox:checked');
            const count = selectedCheckboxes.length;
            
            if (count > 0) {
                bulkActionsBar.style.display = 'block';
                selectedCount.textContent = `${count} selected`;
            } else {
                bulkActionsBar.style.display = 'none';
            }
            
            selectAll.checked = checkboxes.length > 0 && count === checkboxes.length;
        }

        // Delete Modal Functions
        let deleteAnnouncementId = null;

        function confirmDelete(id, name) {
            deleteAnnouncementId = id;
            document.getElementById('deleteAnnouncementName').textContent = name;
            document.getElementById('deleteModal').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.remove('active');
            document.body.style.overflow = 'auto';
            deleteAnnouncementId = null;
        }

        function proceedDelete() {
            if (deleteAnnouncementId) {
                const form = document.getElementById('deleteForm');
                form.action = `/client/announcement/${deleteAnnouncementId}`;
                form.submit();
            }
        }

        // Bulk Publish Modal Functions
        function confirmBulkPublish() {
            const selected = Array.from(document.querySelectorAll('.row-checkbox:checked'));
            if (selected.length === 0) return;
            
            document.getElementById('publishCount').textContent = selected.length;
            document.getElementById('publishModal').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closePublishModal() {
            document.getElementById('publishModal').classList.remove('active');
            document.body.style.overflow = 'auto';
        }

        function proceedPublish() {
            const selected = Array.from(document.querySelectorAll('.row-checkbox:checked'));
            const ids = selected.map(cb => cb.dataset.id);
            
            // Send AJAX request to publish selected announcements
            fetch('/client/announcement/bulk-publish', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ ids: ids })
            })
            .then(response => response.json())
            .then(data => {
                closePublishModal();
                if (data.success) {
                    window.location.reload();
                } else {
                    alert('Error publishing announcements');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error publishing announcements');
            });
        }

        // Bulk Delete Modal Functions
        function confirmBulkDelete() {
            const selected = Array.from(document.querySelectorAll('.row-checkbox:checked'));
            if (selected.length === 0) return;
            
            document.getElementById('bulkDeleteCount').textContent = selected.length;
            document.getElementById('bulkDeleteModal').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeBulkDeleteModal() {
            document.getElementById('bulkDeleteModal').classList.remove('active');
            document.body.style.overflow = 'auto';
        }

        function proceedBulkDelete() {
            const selected = Array.from(document.querySelectorAll('.row-checkbox:checked'));
            const ids = selected.map(cb => cb.dataset.id);
            
            // Send AJAX request to delete selected announcements
            fetch('/client/announcement/bulk-delete', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ ids: ids })
            })
            .then(response => response.json())
            .then(data => {
                closeBulkDeleteModal();
                if (data.success) {
                    window.location.reload();
                } else {
                    alert('Error deleting announcements');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error deleting announcements');
            });
        }

        // Close modals when clicking outside
        document.querySelectorAll('.confirm-modal').forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.remove('active');
                    document.body.style.overflow = 'auto';
                }
            });
        });

        // Close modals with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('.confirm-modal.active').forEach(modal => {
                    modal.classList.remove('active');
                    document.body.style.overflow = 'auto';
                });
            }
        });

        // Auto-hide success/error messages after 5 seconds
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    </script>

    @include('layouts.core.footer')
</body>
</html>