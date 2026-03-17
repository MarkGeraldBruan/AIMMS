<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">

<div class="welcome-section">
    <h2><strong>@if(auth()->user()->isAdmin()) Welcome to Admin Dashboard @else Welcome to the Dashboard @endif</strong></h2>
</div>



@if(auth()->user()->isAdmin())
<div class="stats-container">
    <div class="stat-card green" onclick="navigateTo('/client/supplies')">
        <div class="stat-left">
            <div class="stat-icon">
                <ion-icon name="clipboard"></ion-icon>
            </div>
            <div class="stat-content">
                <h3>Total Items</h3>
                <span class="stat-number">{{ $totalItems }}</span>
            </div>
        </div>
        <div class="arrow-icon">
            <ion-icon name="chevron-forward"></ion-icon>
        </div>
    </div>
    <div class="stat-card green" onclick="navigateTo('/client/users')">
        <div class="stat-left">
            <div class="stat-icon">
                <ion-icon name="people"></ion-icon>
            </div>
            <div class="stat-content">
                <h3>Active Users</h3>
                <span class="stat-number">{{ $totalUsers }}</span>
            </div>
        </div>
        <div class="arrow-icon">
            <ion-icon name="chevron-forward"></ion-icon>
        </div>
    </div>
    <div class="stat-card green" onclick="navigateTo('/client/supplies')">
        <div class="stat-left">
            <div class="stat-icon">
                <ion-icon name="cube"></ion-icon>
            </div>
            <div class="stat-content">
                <h3>Items in Stock</h3>
                <span class="stat-number">{{ $itemsInStock}}</span>
            </div>
        </div>
        <div class="arrow-icon">
            <ion-icon name="chevron-forward"></ion-icon>
        </div>
    </div>
</div>
@endif

<!-- Upcoming Events Section for Non-Admin Users -->
<div class="upcoming-events-section">
    <div class="section-header">
        <h3>
            <ion-icon name="calendar-outline"></ion-icon>
            Upcoming Events
        </h3>
        <span class="events-count">{{ count($publishedAnnouncements ?? []) }} events</span>
    </div>
    
    <div class="events-container @if(count($publishedAnnouncements ?? []) >= 3) scrollable @endif">
        @forelse($publishedAnnouncements ?? [] as $announcement)
            <div class="event-timeline-item">
                <!-- Timeline dot -->
                <div class="timeline-marker">
                    <div class="timeline-dot"></div>
                    @if(!$loop->last)
                        <div class="timeline-line"></div>
                    @endif
                </div>
                
                <!-- Event content -->
                <div class="event-card">
                    <div class="event-top">
                        <div class="event-info">
                            <h4 class="event-title">{{ $announcement->title }}</h4>
                            <div class="event-meta">
                                <span class="meta-item">
                                    <ion-icon name="time"></ion-icon>
                                    {{ $announcement->created_at->diffForHumans() }}
                                </span>
                                @if($announcement->event_date)
                                    <span class="meta-item">
                                        <ion-icon name="calendar"></ion-icon>
                                        {{ $announcement->event_date->format('M d, Y') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        @if($announcement->event_date)
                            <div class="event-badge">
                                <div class="badge-day">{{ $announcement->event_date->format('d') }}</div>
                                <div class="badge-month">{{ $announcement->event_date->format('M') }}</div>
                            </div>
                        @endif
                    </div>
                    
                    <div class="event-description">
                        {{ Str::limit($announcement->content, 150) }}
                    </div>
                    
                    @if(strlen($announcement->content) > 150)
                        <button class="read-more-btn" onclick="showEventModal({{ $announcement->id }}, '{{ addslashes($announcement->title) }}', '{{ addslashes($announcement->content) }}', '{{ $announcement->event_date ? $announcement->event_date->format('M d, Y') : '' }}', '{{ $announcement->created_at->format('M d, Y g:i A') }}')">
                            <ion-icon name="arrow-forward"></ion-icon>
                            Read More
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <div class="no-events-state">
                <ion-icon name="calendar-outline"></ion-icon>
                <h3>No Upcoming Events</h3>
                <p>Check back soon for new announcements and events</p>
            </div>
        @endforelse
    </div>
</div>

<div class="main-content-area">
    <div class="recently-added-section">
        <div class="section-header">
            <h3>
                <ion-icon name="time"></ion-icon>
                Recently Added
            </h3>
        </div>
        <div class="table-container">
            <div class="table-headers">
                <div class="header-cell items-header">
                    <ion-icon name="bag"></ion-icon>
                    Items
                </div>
                <div class="header-cell quantity-header">
                    <ion-icon name="layers"></ion-icon>
                    Quantity
                </div>
                <div class="header-cell date-header">
                    <ion-icon name="calendar"></ion-icon>
                    Date
                </div>
            </div>

            <div class="table-body @if(count($recentItems ?? []) >= 5) scrollable @endif">
                @forelse($recentItems ?? [] as $item)
                    <div class="table-row">
                        <div class="cell items-cell">{{ $item->name ?? 'Sample Item' }}</div>
                        <div class="cell quantity-cell">
                            <span class="quantity-badge">{{ $item->quantity ?? 25 }}</span>
                        </div>
                        <div class="cell date-cell">{{ $item->created_at?->format('M d, Y') ?? 'Dec 15, 2024' }}</div>
                    </div>
                @empty
                @endforelse
            </div>
        </div>
    </div>
    
    <div class="right-panel">
        <div class="low-stock-panel">
            <div class="low-stock-header">
                <ion-icon name="warning"></ion-icon>
                Low Stock Alert
            </div>
            
            <div class="low-stock-headers">
                <div class="low-header id-header">
                    <ion-icon name="finger-print"></ion-icon>
                    ID
                </div>
                <div class="low-header product-header">
                    <ion-icon name="bag-handle"></ion-icon>
                    Product
                </div>
                <div class="low-header qty-header">
                    <ion-icon name="analytics"></ion-icon>
                    Qty
                </div>
            </div>
            
            <div class="low-stock-body @if(count($lowStockItems ?? []) > 5) scrollable @endif">
                @forelse($lowStockItems ?? [] as $item)
                    <div class="low-stock-row">
                        <div class="low-cell id-cell">{{ $loop->iteration }}</div>
                        <div class="low-cell product-cell">
                            <span class="product-indicator"></span>
                            {{ $item->name ?? 'Product' }}
                        </div>
                        <div class="low-cell qty-cell">
                            <span class="low-quantity">{{ $item->quantity ?? 0 }}</span>
                        </div>
                    </div>
                    @empty
                @endforelse
            </div>
            
            <button class="restock-btn" onclick="navigateTo('/client/supplies?low_stock=1')">
                <ion-icon name="add-circle"></ion-icon>
                Restock Items
            </button>
        </div>
    </div>
</div>

<!-- Event Modal -->
<div id="eventModal" class="event-modal">
    <div class="event-modal-content">
        <div class="event-modal-header">
            <h3 id="eventModalTitle"></h3>
            <button class="event-modal-close" onclick="closeEventModal()">
                <ion-icon name="close"></ion-icon>
            </button>
        </div>
        <div class="event-modal-body">
            <div class="event-modal-meta">
                <div class="modal-meta-item" id="eventModalDateItem" style="display: none;">
                    <ion-icon name="calendar"></ion-icon>
                    <span id="eventModalDate"></span>
                </div>
                <div class="modal-meta-item">
                    <ion-icon name="time"></ion-icon>
                    <span id="eventModalCreatedDate"></span>
                </div>
            </div>
            <div class="event-modal-description">
                <p id="eventModalContent"></p>
            </div>
        </div>
    </div>
</div>

<style>
/* Upcoming Events Section */
.upcoming-events-section {
    background: white;
    border-radius: 12px;
    padding: 0;
    margin-bottom: 30px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    overflow: hidden;
}

.upcoming-events-section .section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: linear-gradient(135deg, #296218 0%, #1e4612 100%);
    color: white;
    padding: 20px 25px;
    border-bottom: 3px solid #ffc107;
    margin: 0;
}

.upcoming-events-section .section-header h3 {
    display: flex;
    align-items: center;
    gap: 10px;
    color: white;
    font-size: 20px;
    margin: 0;
}

.upcoming-events-section .section-header ion-icon {
    font-size: 24px;
}

.events-count {
    background: rgba(255, 255, 255, 0.2);
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.events-container {
    padding: 25px;
    position: relative;
}

.events-container.scrollable {
    max-height: 500px;
    overflow-y: auto;
}

/* Timeline Layout */
.event-timeline-item {
    display: flex;
    gap: 20px;
    margin-bottom: 25px;
    animation: slideInLeft 0.5s ease-out forwards;
    opacity: 0;
}

.event-timeline-item:nth-child(1) { animation-delay: 0.1s; }
.event-timeline-item:nth-child(2) { animation-delay: 0.2s; }
.event-timeline-item:nth-child(3) { animation-delay: 0.3s; }
.event-timeline-item:nth-child(4) { animation-delay: 0.4s; }
.event-timeline-item:nth-child(5) { animation-delay: 0.5s; }
.event-timeline-item:nth-child(6) { animation-delay: 0.6s; }

@keyframes slideInLeft {
    from {
        opacity: 0;
        transform: translateX(-20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.timeline-marker {
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
    flex-shrink: 0;
    width: 20px;
}

.timeline-dot {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: linear-gradient(135deg, #296218 0%, #1e4612 100%);
    box-shadow: 0 0 0 4px rgba(41, 98, 24, 0.1);
    position: relative;
    z-index: 2;
}

.timeline-line {
    flex: 1;
    width: 2px;
    background: linear-gradient(180deg, #296218 0%, #e9ecef 100%);
    margin-top: 5px;
}

/* Event Card */
.event-card {
    flex: 1;
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    border: 1px solid #e9ecef;
    border-radius: 10px;
    padding: 18px;
    transition: all 0.3s ease;
    border-left: 3px solid #296218;
}

.event-card:hover {
    box-shadow: 0 8px 25px rgba(41, 98, 24, 0.15);
    transform: translateY(-3px);
}

.event-top {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 15px;
    margin-bottom: 12px;
}

.event-info {
    flex: 1;
}

.event-title {
    color: #296218;
    font-size: 16px;
    font-weight: 600;
    margin: 0 0 8px 0;
    word-break: break-word;
}

.event-meta {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 5px;
    color: #6c757d;
    font-size: 12px;
}

.meta-item ion-icon {
    font-size: 14px;
    color: #296218;
}

.event-badge {
    background: linear-gradient(135deg, #ffc107 0%, #ffb300 100%);
    color: #1e4612;
    padding: 8px 12px;
    border-radius: 8px;
    text-align: center;
    font-weight: 600;
    box-shadow: 0 2px 8px rgba(255, 193, 7, 0.3);
    flex-shrink: 0;
}

.badge-day {
    font-size: 16px;
    line-height: 1;
}

.badge-month {
    font-size: 10px;
    opacity: 0.8;
    margin-top: 2px;
}

.event-description {
    color: #495057;
    font-size: 13px;
    line-height: 1.6;
    margin-bottom: 12px;
}

.read-more-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #296218;
    color: white;
    border: none;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-weight: 500;
}

.read-more-btn:hover {
    background: #1e4612;
    transform: translateX(3px);
}

.read-more-btn ion-icon {
    font-size: 14px;
}

/* No Events State */
.no-events-state {
    text-align: center;
    padding: 60px 30px;
    color: #6c757d;
}

.no-events-state ion-icon {
    font-size: 64px;
    color: #dee2e6;
    margin-bottom: 15px;
    display: block;
}

.no-events-state h3 {
    font-size: 18px;
    color: #495057;
    margin-bottom: 8px;
}

.no-events-state p {
    font-size: 14px;
    margin: 0;
}

/* Event Modal */
.event-modal {
    display: none;
    position: fixed;
    z-index: 10000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(4px);
    animation: fadeIn 0.3s ease;
}

.event-modal.active {
    display: flex;
    align-items: center;
    justify-content: center;
}

.event-modal-content {
    background-color: white;
    border-radius: 15px;
    width: 90%;
    max-width: 600px;
    max-height: 80vh;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    animation: slideUp 0.3s ease;
}

.event-modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 25px;
    background: linear-gradient(135deg, #296218 0%, #1e4612 100%);
    color: white;
    border-bottom: 3px solid #ffc107;
}

.event-modal-header h3 {
    margin: 0;
    font-size: 22px;
    flex: 1;
}

.event-modal-close {
    background: rgba(255, 255, 255, 0.2);
    border: none;
    color: white;
    width: 35px;
    height: 35px;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.event-modal-close:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: rotate(90deg);
}

.event-modal-close ion-icon {
    font-size: 24px;
}

.event-modal-body {
    padding: 30px;
    overflow-y: auto;
    max-height: calc(80vh - 100px);
}

.event-modal-meta {
    display: flex;
    gap: 20px;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid #e9ecef;
    flex-wrap: wrap;
}

.modal-meta-item {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #6c757d;
    font-size: 13px;
}

.modal-meta-item ion-icon {
    font-size: 18px;
    color: #296218;
}

.event-modal-description p {
    color: #495057;
    line-height: 1.8;
    font-size: 15px;
    margin: 0;
    white-space: pre-wrap;
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

.scrollable {
    max-height: 250px;
    overflow-y: auto;
}

@media (max-width: 768px) {
    .event-top {
        flex-direction: column;
    }

    .event-card {
        padding: 15px;
    }

    .event-modal-content {
        width: 95%;
        max-height: 90vh;
    }

    .event-modal-header {
        padding: 20px;
    }

    .event-modal-body {
        padding: 20px;
    }
}
</style>

<script>
function navigateTo(url) {
    window.location.href = url;
}

function showLowStockDetails() {
    document.querySelector('.low-stock-panel').scrollIntoView({ 
        behavior: 'smooth',
        block: 'center'
    });
    
    const panel = document.querySelector('.low-stock-panel');
    panel.style.boxShadow = '0 0 30px rgba(255, 193, 7, 0.4)';
    setTimeout(() => {
        panel.style.boxShadow = '0 4px 20px rgba(0,0,0,0.08)';
    }, 2000);
}

function showEventModal(id, title, content, eventDate, createdDate) {
    const modal = document.getElementById('eventModal');
    document.getElementById('eventModalTitle').textContent = title;
    document.getElementById('eventModalContent').textContent = content;
    document.getElementById('eventModalCreatedDate').textContent = createdDate;
    
    const dateItem = document.getElementById('eventModalDateItem');
    if (eventDate) {
        document.getElementById('eventModalDate').textContent = eventDate;
        dateItem.style.display = 'flex';
    } else {
        dateItem.style.display = 'none';
    }
    
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeEventModal() {
    const modal = document.getElementById('eventModal');
    modal.classList.remove('active');
    document.body.style.overflow = 'auto';
}

// Close modal when clicking outside
document.getElementById('eventModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeEventModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeEventModal();
    }
});
</script>

@include('layouts.core.footer')
