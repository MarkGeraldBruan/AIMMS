<div class="dashboard-header">
    <div class="header-left">
        <span class="system-title">Monitoring Management System</span>
        <span class="system-subtitle">Agricultural Training Institute - Regional Training Center 1</span>
    </div>
    <div class="header-right">
        <div class="notifications" onclick="toggleNotifications()">
            <span class="icon"><ion-icon name="notifications-outline"></ion-icon></span>
            <div class="notification-badge" id="notificationCount">{{ Auth::user()->unreadNotifications()->count() }}</div>
            
            <!-- Notification Dropdown -->
            <div class="notification-dropdown" id="notificationDropdown">
                <div class="notification-header">
                    <h4>Notifications</h4>
                    <button class="mark-all-read" onclick="markAllAsRead()">Mark all as read</button>
                </div>
                <div class="notification-list" id="notificationList">
                    <!-- Notifications will be loaded here -->
                </div>
                <div class="notification-footer">
                    <a href="{{ route('notifications.index') }}">View all notifications</a>
                </div>
            </div>
        </div>
        <div class="user-profile">
            <div class="user-avatar">
                <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}" class="avatar-image">
            </div>
            <div class="user-info">
                <span class="username">{{ Auth::user()->name }}</span>
                <span class="user-email">{{ Auth::user()->email }}</span>
            </div>
        </div>
    </div>
</div>

<style>
/* Dashboard Header - Fixed positioning and z-index */
.dashboard-header {
    background: #296218;
    color: white;
    padding: 20px 30px;
    border-radius: 10px;
    margin-bottom: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 4px 15px rgba(41, 98, 24, 0.3);
    position: relative;
    z-index: 50;
    width: 100%;
    box-sizing: border-box;
    min-width: 0;
    flex-wrap: nowrap;
}

.header-left .system-title {
    font-size: 22px;
    font-weight: 600;
    display: block;
    margin-bottom: 4px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    flex-shrink: 1;
    min-width: 0;
}

.header-left .system-subtitle {
    font-size: 13px;
    opacity: 0.9;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    flex-shrink: 1;
    min-width: 0;
}

.header-right {
    display: flex;
    align-items: center;
    gap: 20px;
    position: relative;
    z-index: 51;
    flex-shrink: 0;
}

/* Notifications container */
.notifications {
    position: relative;
    cursor: pointer;
    background: rgba(255, 255, 255, 0.2);
    padding: 12px;
    border-radius: 50%;
    transition: all 0.3s ease;
    z-index: 52;
}

.notifications:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: scale(1.05);
}

.notifications .icon {
    display: flex;
    align-items: center;
    justify-content: center;
}

.notifications .icon ion-icon {
    font-size: 20px;
    color: white;
}

/* Notification badge */
.notification-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    background: #dc3545;
    color: white;
    border-radius: 50%;
    width: 18px;
    height: 18px;
    font-size: 11px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    opacity: 1;
    transition: all 0.3s ease;
}

.notification-badge:empty,
.notification-badge[data-count="0"] {
    opacity: 0;
}

/* Notification Dropdown - Fixed positioning */
.notification-dropdown {
    position: absolute;
    top: 55px;
    right: 0;
    width: 350px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    z-index: 9999;
    opacity: 0;
    visibility: hidden;
    transform: translateY(-20px);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid #e0e0e0;
    overflow: hidden;
    pointer-events: none;
}

.notification-dropdown.active {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
    pointer-events: auto;
}

/* Dropdown header */
.notification-header {
    padding: 20px 20px 15px;
    border-bottom: 1px solid #eee;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #fafafa;
}

.notification-header h4 {
    margin: 0;
    color: #333;
    font-size: 16px;
    font-weight: 600;
}

.mark-all-read {
    background: none;
    border: none;
    color: #296218;
    font-size: 12px;
    cursor: pointer;
    font-weight: 500;
    transition: color 0.3s ease;
    padding: 4px 8px;
    border-radius: 4px;
}

.mark-all-read:hover {
    color: #1e4612;
    background: rgba(41, 98, 24, 0.1);
}

/* Notification list */
.notification-list {
    max-height: 400px;
    overflow-y: auto;
    background: white;
}

.notification-item {
    padding: 15px 20px;
    border-bottom: 1px solid #f0f0f0;
    cursor: pointer;
    transition: all 0.3s ease;
    background: white;
}

.notification-item:hover {
    background-color: #f8f9fa;
}

.notification-item:last-child {
    border-bottom: none;
}

.notification-item.unread {
    background-color: #f0f8ff;
    border-left: 3px solid #296218;
}

.notification-item.unread:hover {
    background-color: #e6f3ff;
}

.notification-title {
    font-weight: 600;
    color: #333;
    font-size: 14px;
    margin-bottom: 4px;
    line-height: 1.3;
}

.notification-message {
    color: #666;
    font-size: 13px;
    line-height: 1.4;
    margin-bottom: 6px;
}

.notification-time {
    color: #999;
    font-size: 11px;
}

/* Dropdown footer */
.notification-footer {
    padding: 15px 20px;
    text-align: center;
    border-top: 1px solid #eee;
    background: #fafafa;
}

.notification-footer a {
    color: #296218;
    text-decoration: none;
    font-size: 13px;
    font-weight: 500;
    transition: color 0.3s ease;
}

.notification-footer a:hover {
    color: #1e4612;
    text-decoration: underline;
}

/* Empty notifications state */
.empty-notifications {
    padding: 40px 20px;
    text-align: center;
    color: #999;
}

.empty-notifications ion-icon {
    font-size: 48px;
    margin-bottom: 15px;
    opacity: 0.3;
    color: #ccc;
}

.empty-notifications p {
    margin: 0;
    font-size: 14px;
}

/* User profile */
.user-profile {
    display: flex;
    align-items: center;
    gap: 10px;
}

.user-avatar {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    background: white;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    border: 2px solid rgba(255, 255, 255, 0.3);
}

.avatar-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%;
}

.user-info {
    display: flex;
    flex-direction: column;
    min-width: 0;
    flex: 1;
}

.username {
    font-weight: 600;
    font-size: 14px;
    margin-bottom: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.user-email {
    font-size: 12px;
    opacity: 0.8;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Scrollbar styling */
.notification-list::-webkit-scrollbar {
    width: 6px;
}

.notification-list::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.notification-list::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.notification-list::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Responsive Design */
@media (max-width: 768px) {
    .notification-dropdown {
        width: 320px;
        right: -20px;
        left: auto;
    }
    
    .header-right {
        gap: 10px;
    }
    
    .user-info {
        display: none;
    }
    
    .dashboard-header {
        padding: 15px 20px;
    }
    
    .header-left .system-title {
        font-size: 18px;
    }
    
    .header-left .system-subtitle {
        font-size: 12px;
    }
}

@media (max-width: 480px) {
    .notification-dropdown {
        width: 280px;
        right: -10px;
    }
}
</style>

<script>
let notificationDropdown = null;
let isDropdownOpen = false;

document.addEventListener('DOMContentLoaded', function() {
    notificationDropdown = document.getElementById('notificationDropdown');
    loadNotifications();
    
    // Update notification count periodically
    setInterval(updateNotificationCount, 30000); // Every 30 seconds
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.notifications') && isDropdownOpen) {
            closeNotificationDropdown();
        }
    });
    
    // Prevent dropdown from closing when clicking inside it
    if (notificationDropdown) {
        notificationDropdown.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }
});

function toggleNotifications(e) {
    if (e) {
        e.stopPropagation();
    }
    
    if (isDropdownOpen) {
        closeNotificationDropdown();
    } else {
        openNotificationDropdown();
    }
}

function openNotificationDropdown() {
    if (notificationDropdown) {
        notificationDropdown.classList.add('active');
        isDropdownOpen = true;
        loadNotifications();
    }
}

function closeNotificationDropdown() {
    if (notificationDropdown) {
        notificationDropdown.classList.remove('active');
        isDropdownOpen = false;
    }
}

function loadNotifications() {
    fetch('/client/notifications/recent')
        .then(response => response.json())
        .then(data => {
            displayNotifications(data.notifications);
        })
        .catch(error => {
            console.error('Error loading notifications:', error);
            displayNotifications([]);
        });
}

function displayNotifications(notifications) {
    const notificationList = document.getElementById('notificationList');
    
    if (!notificationList) return;
    
    if (notifications.length === 0) {
        notificationList.innerHTML = `
            <div class="empty-notifications">
                <ion-icon name="notifications-off-outline"></ion-icon>
                <p>No notifications yet</p>
            </div>
        `;
        return;
    }
    
    notificationList.innerHTML = notifications.map(notification => `
        <div class="notification-item ${!notification.is_read ? 'unread' : ''}" 
             onclick="markAsRead(${notification.id}, '${getNotificationUrl(notification)}')">
            <div class="notification-title">${escapeHtml(notification.title)}</div>
            <div class="notification-message">${escapeHtml(notification.message)}</div>
            <div class="notification-time">${notification.created_date}</div>
        </div>
    `).join('');
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function getNotificationUrl(notification) {
    if (notification.type === 'help_request' || notification.type === 'help_response') {
        if (notification.data && notification.data.help_request_id) {
            return `/client/help/${notification.data.help_request_id}`;
        }
    }
    return '#';
}

function markAsRead(notificationId, url) {
    fetch(`/client/notifications/${notificationId}/mark-as-read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateNotificationCount();
            closeNotificationDropdown();
            if (url && url !== '#') {
                window.location.href = url;
            }
        }
    })
    .catch(error => console.error('Error marking notification as read:', error));
}

function markAllAsRead() {
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
            updateNotificationCount();
            loadNotifications();
        }
    })
    .catch(error => console.error('Error marking all notifications as read:', error));
}

function updateNotificationCount() {
    fetch('/client/notifications/unread-count')
        .then(response => response.json())
        .then(data => {
            const badge = document.getElementById('notificationCount');
            if (badge) {
                badge.textContent = data.count;
                badge.style.opacity = data.count > 0 ? '1' : '0';
                badge.setAttribute('data-count', data.count);
            }
        })
        .catch(error => console.error('Error updating notification count:', error));
}
</script>