<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Announcement</title>
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

            <div class="form-container">
                <!-- Header Section -->
                <div class="form-header">
                    <a href="{{ route('client.announcement.index') }}" class="back-button">
                        <i class="fas fa-arrow-left"></i>
                        Back to Announcements
                    </a>
                    <h1 class="form-title">
                        <i class="fas fa-bullhorn"></i>
                        Announcement Details
                    </h1>
                </div>

                <!-- Success/Error Messages -->
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

                <!-- Announcement Details Card -->
                <div class="form-content" style="padding: 30px;">
                    <div class="announcement-header" style="border-bottom: 2px solid #e9ecef; padding-bottom: 20px; margin-bottom: 25px;">
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 15px;">
                            <h2 style="color: #296218; font-size: 28px; margin: 0;">{{ $announcement->title }}</h2>
                            <span class="status-badge {{ $announcement->status === 'published' ? 'status-published' : 'status-draft' }}">
                                <i class="fas fa-circle" style="font-size: 8px;"></i>
                                {{ ucfirst($announcement->status) }}
                            </span>
                        </div>
                    </div>

                    <div class="details-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
                        <div class="detail-item">
                            <div style="display: flex; align-items: center; gap: 8px; color: #6c757d; font-size: 14px; margin-bottom: 5px;">
                                <i class="fas fa-calendar-plus"></i>
                                <span style="font-weight: 600;">Date Created</span>
                            </div>
                            <div style="color: #495057; font-size: 15px;">
                                {{ $announcement->created_at->format('F d, Y') }}
                                <span style="color: #6c757d;">at {{ $announcement->created_at->format('g:i A') }}</span>
                            </div>
                        </div>

                        <div class="detail-item">
                            <div style="display: flex; align-items: center; gap: 8px; color: #6c757d; font-size: 14px; margin-bottom: 5px;">
                                <i class="fas fa-calendar-check"></i>
                                <span style="font-weight: 600;">Last Updated</span>
                            </div>
                            <div style="color: #495057; font-size: 15px;">
                                {{ $announcement->updated_at->format('F d, Y') }}
                                <span style="color: #6c757d;">at {{ $announcement->updated_at->format('g:i A') }}</span>
                            </div>
                        </div>

                        @if($announcement->event_date)
                        <div class="detail-item">
                            <div style="display: flex; align-items: center; gap: 8px; color: #6c757d; font-size: 14px; margin-bottom: 5px;">
                                <i class="fas fa-calendar-day"></i>
                                <span style="font-weight: 600;">Event Date</span>
                            </div>
                            <div style="color: #495057; font-size: 15px;">
                                {{ $announcement->event_date->format('F d, Y') }}
                            </div>
                        </div>
                        @endif

                        @if($announcement->creator)
                        <div class="detail-item">
                            <div style="display: flex; align-items: center; gap: 8px; color: #6c757d; font-size: 14px; margin-bottom: 5px;">
                                <i class="fas fa-user"></i>
                                <span style="font-weight: 600;">Created By</span>
                            </div>
                            <div style="color: #495057; font-size: 15px;">
                                {{ $announcement->creator->name }}
                            </div>
                        </div>
                        @endif
                    </div>

                    <div class="content-section" style="background: #f8f9fa; padding: 25px; border-radius: 8px; border-left: 4px solid #296218; margin-bottom: 30px;">
                        <div style="display: flex; align-items: center; gap: 8px; color: #495057; font-weight: 600; margin-bottom: 15px;">
                            <i class="fas fa-align-left"></i>
                            <span>Content</span>
                        </div>
                        <div style="color: #495057; line-height: 1.8; white-space: pre-wrap; font-size: 15px;">
                            {{ $announcement->content }}
                        </div>
                    </div>

                    <!-- Supplies Section -->
                    @if($announcement->supplies->count() > 0)
                    <div class="supplies-section" style="margin-bottom: 30px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                            <h3 style="color: #296218; font-size: 20px; margin: 0;">
                                <i class="fas fa-boxes"></i>
                                Supplies Needed
                            </h3>
                            @if($announcement->supplies()->where('status', 'pending')->exists())
                            <div style="display: flex; gap: 10px;">
                                <button type="button" class="btn btn-warning btn-sm" onclick="reserveSupplies()">
                                    <i class="fas fa-lock"></i>
                                    Reserve Supplies
                                </button>
                                <button type="button" class="btn btn-danger btn-sm" onclick="confirmStockOut()">
                                    <i class="fas fa-arrow-down"></i>
                                    Stock Out Now
                                </button>
                            </div>
                            @endif
                        </div>

                        <div class="supplies-table-wrapper" style="overflow-x: auto;">
                            <table class="supplies-table" style="width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden;">
                                <thead>
                                    <tr style="background: #296218; color: white;">
                                        <th style="padding: 12px; text-align: left;">Item Name</th>
                                        <th style="padding: 12px; text-align: center; width: 120px;">Qty Needed</th>
                                        <th style="padding: 12px; text-align: center; width: 120px;">Available</th>
                                        <th style="padding: 12px; text-align: center; width: 120px;">Status</th>
                                        <th style="padding: 12px; text-align: left;">Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($announcement->supplies as $supply)
                                    <tr style="border-bottom: 1px solid #e9ecef;">
                                        <td style="padding: 12px;">
                                            <strong>{{ $supply->name }}</strong>
                                            <br>
                                            <small style="color: #6c757d;">{{ $supply->category }}</small>
                                        </td>
                                        <td style="padding: 12px; text-align: center;">
                                            <span style="font-weight: 600; color: #296218;">
                                                {{ $supply->pivot->quantity_needed }} {{ $supply->unit }}
                                            </span>
                                        </td>
                                        <td style="padding: 12px; text-align: center;">
                                            <span class="{{ $supply->quantity < $supply->pivot->quantity_needed ? 'text-danger' : 'text-success' }}" style="font-weight: 600;">
                                                {{ $supply->quantity }} {{ $supply->unit }}
                                            </span>
                                        </td>
                                        <td style="padding: 12px; text-align: center;">
                                            @php
                                                $statusColors = [
                                                    'pending' => 'warning',
                                                    'reserved' => 'info',
                                                    'used' => 'success',
                                                    'returned' => 'secondary'
                                                ];
                                                $statusColor = $statusColors[$supply->pivot->status] ?? 'secondary';
                                            @endphp
                                            <span class="badge badge-{{ $statusColor }}" style="padding: 5px 10px; border-radius: 12px; font-size: 12px; text-transform: uppercase;">
                                                {{ ucfirst($supply->pivot->status) }}
                                            </span>
                                        </td>
                                        <td style="padding: 12px;">
                                            @if($supply->pivot->status === 'used')
                                                <small style="color: #6c757d;">
                                                    Used on {{ \Carbon\Carbon::parse($supply->pivot->used_at)->format('M d, Y g:i A') }}
                                                </small>
                                            @elseif($supply->pivot->status === 'reserved')
                                                <small style="color: #6c757d;">
                                                    Reserved on {{ \Carbon\Carbon::parse($supply->pivot->reserved_at)->format('M d, Y g:i A') }}
                                                </small>
                                            @elseif($supply->quantity < $supply->pivot->quantity_needed)
                                                <small style="color: #dc3545; font-weight: 600;">
                                                    <i class="fas fa-exclamation-triangle"></i>
                                                    Insufficient stock
                                                </small>
                                            @else
                                                <small style="color: #28a745;">
                                                    <i class="fas fa-check-circle"></i>
                                                    Available
                                                </small>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Stock Availability Summary -->
                        <div style="margin-top: 15px; padding: 15px; background: {{ $announcement->hasAvailableStock() ? '#d4edda' : '#f8d7da' }}; border-radius: 8px; border: 1px solid {{ $announcement->hasAvailableStock() ? '#c3e6cb' : '#f5c6cb' }};">
                            @if($announcement->hasAvailableStock())
                                <div style="display: flex; align-items: center; gap: 10px; color: #155724;">
                                    <i class="fas fa-check-circle" style="font-size: 20px;"></i>
                                    <span style="font-weight: 600;">All supplies are available for this event.</span>
                                </div>
                            @else
                                <div style="display: flex; align-items: center; gap: 10px; color: #721c24;">
                                    <i class="fas fa-exclamation-triangle" style="font-size: 20px;"></i>
                                    <span style="font-weight: 600;">Some supplies have insufficient stock. Please restock before processing.</span>
                                </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e9ecef; display: flex; justify-content: space-between; align-items: center;">
                        <button type="button" 
                                class="btn btn-danger" 
                                onclick="confirmDelete({{ $announcement->id }}, '{{ addslashes($announcement->title) }}')">
                            <i class="fas fa-trash"></i>
                            Delete Announcement
                        </button>
                        
                        <div style="display: flex; gap: 10px;">
                            <a href="{{ route('client.announcement.edit', $announcement->id) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i>
                                Edit
                            </a>
                            <a href="{{ route('client.announcement.index') }}" class="btn btn-secondary">
                                <i class="fas fa-list"></i>
                                View All
                            </a>
                        </div>
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

    <!-- Stock Out Confirmation Modal -->
    <div id="stockOutModal" class="confirm-modal">
        <div class="confirm-modal-content">
            <div class="confirm-modal-header" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);">
                <i class="fas fa-arrow-down"></i>
                <h3>Confirm Stock Out</h3>
            </div>
            <div class="confirm-modal-body">
                <p>Are you sure you want to process stock out for this event?</p>
                <p style="font-weight: 600; color: #296218;">{{ $announcement->title }}</p>
                <p class="warning-text">This will deduct the following supplies from inventory:</p>
                <ul style="margin: 15px 0; padding-left: 20px;">
                    @foreach($announcement->supplies as $supply)
                    <li style="margin-bottom: 8px;">
                        <strong>{{ $supply->name }}</strong>: {{ $supply->pivot->quantity_needed }} {{ $supply->unit }}
                    </li>
                    @endforeach
                </ul>
                <p class="warning-text">This action cannot be undone!</p>
            </div>
            <div class="confirm-modal-footer">
                <button class="btn btn-secondary" onclick="closeStockOutModal()">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button class="btn btn-danger" onclick="proceedStockOut()">
                    <i class="fas fa-arrow-down"></i> Process Stock Out
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
    /* Status Badge Styles */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-published {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .status-draft {
        background: #fff3cd;
        color: #856404;
        border: 1px solid #ffeeba;
    }

    /* Badge Styles */
    .badge {
        display: inline-block;
        padding: 5px 10px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .badge-warning {
        background: #ffc107;
        color: #000;
    }

    .badge-info {
        background: #17a2b8;
        color: white;
    }

    .badge-success {
        background: #28a745;
        color: white;
    }

    .badge-secondary {
        background: #6c757d;
        color: white;
    }

    .text-danger {
        color: #dc3545 !important;
    }

    .text-success {
        color: #28a745 !important;
    }

    /* Alert Styles */
    .alert {
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 500;
    }

    .alert-success {
        background: #d4edda;
        border: 1px solid #c3e6cb;
        color: #155724;
    }

    .alert-danger {
        background: #f8d7da;
        border: 1px solid #f5c6cb;
        color: #721c24;
    }

    /* Modal Styles */
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
        
        .supplies-table-wrapper {
            overflow-x: auto;
        }
        
        .supplies-table {
            font-size: 13px;
        }

        .details-grid {
            grid-template-columns: 1fr !important;
        }
    }
    </style>

    <script>
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

        function confirmStockOut() {
            @if(!$announcement->hasAvailableStock())
                alert('Cannot process stock out: Some supplies have insufficient stock.');
                return;
            @endif
            
            document.getElementById('stockOutModal').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeStockOutModal() {
            document.getElementById('stockOutModal').classList.remove('active');
            document.body.style.overflow = 'auto';
        }

        function proceedStockOut() {
            fetch('/client/announcement/{{ $announcement->id }}/stock-out', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                closeStockOutModal();
                if (data.success) {
                    window.location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error processing stock out');
            });
        }

        function reserveSupplies() {
            @if(!$announcement->hasAvailableStock())
                alert('Cannot reserve: Some supplies have insufficient stock.');
                return;
            @endif

            if (!confirm('Reserve supplies for this event?')) {
                return;
            }

            fetch('/client/announcement/{{ $announcement->id }}/reserve', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error reserving supplies');
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

        // Auto-hide success/error messages
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
