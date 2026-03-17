<link rel="stylesheet" href="{{ asset('css/style.css') }}">
<link rel="stylesheet" href="{{ asset('css/notifications.css') }}">
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="container">
    @include('layouts.core.sidebar')
    <div class="details">
        @include('layouts.core.header')
        <div class="notifications-container">
            <div class="notifications-header">
                <h1><ion-icon name="notifications-outline"></ion-icon> All Notifications</h1>
                <button class="btn btn-primary" onclick="markAllAsRead()">
                    <ion-icon name="checkmark-done-outline"></ion-icon> Mark All as Read
                </button>
            </div>

            <div class="notifications-content">
                @if($notifications->count() > 0)
                    <div class="notifications-list">
                        @foreach($notifications as $notification)
                        <div class="notification-card {{ !$notification->is_read ? 'unread' : '' }}" 
                            onclick="markAsReadAndRedirect({{ $notification->id }}, '{{ getNotificationUrl($notification) }}')">
                            <div class="notification-icon">
                                @switch($notification->type)
                                    @case('help_request')
                                        <ion-icon name="help-circle-outline" style="color: #dc3545;"></ion-icon>
                                        @break
                                    @case('help_response')
                                        <ion-icon name="chatbubble-outline" style="color: #28a745;"></ion-icon>
                                        @break
                                    @default
                                        <ion-icon name="information-circle-outline" style="color: #17a2b8;"></ion-icon>
                                @endswitch
                            </div>
                            <div class="notification-content">
                                <div class="notification-header">
                                    <h4>{{ $notification->title }}</h4>
                                    <span class="notification-time">{{ $notification->created_date }}</span>
                                </div>
                                <p class="notification-message">{{ $notification->message }}</p>
                                @if($notification->data && isset($notification->data['priority']))
                                    <div class="notification-meta">
                                        <span class="priority-badge priority-{{ $notification->data['priority'] }}">
                                            {{ ucfirst($notification->data['priority']) }} Priority
                                        </span>
                                    </div>
                                @endif
                            </div>
                            @if(!$notification->is_read)
                                <div class="unread-indicator"></div>
                            @endif
                        </div>
                        @endforeach
                    </div>

                    <div class="pagination-wrapper">
                        {{ $notifications->links() }}
                    </div>
                @else
                    <div class="empty-state">
                        <ion-icon name="notifications-off-outline"></ion-icon>
                        <h3>No notifications yet</h3>
                        <p>You'll see notifications here when there are updates or responses to your help requests.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
function markAsReadAndRedirect(notificationId, url) {
    fetch(`/client/notifications/${notificationId}/mark-as-read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && url && url !== '#') {
            window.location.href = url;
        } else if (data.success) {
            location.reload();
        }
    })
    .catch(error => console.error('Error marking notification as read:', error));
}

function markAllAsRead() {
    if (confirm('Mark all notifications as read?')) {
        fetch('/client/notifications/mark-all-read', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        })
        .catch(error => console.error('Error marking all notifications as read:', error));
    }
}

function getNotificationUrl(notification) {
    if (notification.type === 'help_request' || notification.type === 'help_response') {
        if (notification.data && notification.data.help_request_id) {
            return `/client/help/${notification.data.help_request_id}`;
        }
    }
    return '#';
}
</script>

@php
function getNotificationUrl($notification) {
    if ($notification->type === 'help_request' || $notification->type === 'help_response') {
        if ($notification->data && isset($notification->data['help_request_id'])) {
            return route('client.help.show', $notification->data['help_request_id']); // Changed from 'help.show' to 'client.help.show'
        }
    }
    return '#';
}
@endphp