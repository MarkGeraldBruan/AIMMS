{{-- filepath: resources/views/layouts/core/sidebar.blade.php --}}

<div class="navigation">
    <ul>
        <li>
            <a href="#">
                <span class="brand-container">
                    <img src="{{ auth()->check() ? auth()->user()->logo_url : asset('assets/img/atirtc1logo.jpg') }}" alt="AGRISUPPLY Logo" class="brand-logo">
                </span>
                <span class="title">ATI-RTC 1</span>
            </a>
        </li>
        <li class="{{ Request::is('client/dashboard*') || Request::is('home') || Request::is('/') ? 'hovered' : '' }}">
            <a href="{{ route('client.dashboard') }}" class="nav-link">
                <span class="icon"><ion-icon name="home-outline"></ion-icon></span>
                <span class="title">Dashboard</span>
            </a>
        </li>

        @if(auth()->check() && auth()->user()->isAdmin())
        <li class="{{ Request::is('client/announcement*') ? 'hovered' : '' }}">
            <a href="{{ route('client.announcement.index') }}">
                <span class="icon"><ion-icon name="megaphone-outline"></ion-icon></span>
                <span class="title">Announcements</span>
            </a>
        </li>
        @endif
        
        @if(auth()->check() && auth()->user()->isAdmin())
            <li class="{{ Request::is('client/users*') ? 'hovered' : '' }}">
                <a href="{{ url('client/users') }}" class="nav-link">
                    <span class="icon"><ion-icon name="people-outline"></ion-icon></span>
                    <span class="title">Manage Users</span>
                </a>
            </li>
        @endif
        
        <li class="{{ Request::is('client/supplies*') ? 'hovered' : '' }}">
            <a href="{{ url('client/supplies') }}">
                <span class="icon"><ion-icon name="archive-outline"></ion-icon></span>
                <span class="title">Supplies</span>
            </a>
        </li>
        <li class="{{ Request::is('client/equipment*') ? 'hovered' : '' }}">
            <a href="{{ url('client/equipment') }}">
                <span class="icon"><ion-icon name="construct-outline"></ion-icon></span>
                <span class="title">Equipment</span>
            </a>
        </li>
        <li class="{{ Request::is('client/reports*') ? 'hovered' : '' }}">
            <a href="{{ route('client.reports.index') }}">
                <span class="icon"><ion-icon name="stats-chart-outline"></ion-icon></span>
                <span class="title">Report</span>
            </a>
        </li>
        <li class="dropdown {{ Request::is('client/stockcard*') || Request::is('client/propertycard*') ? 'active' : '' }}">
            <a href="#" onclick="toggleDropdown(event, this)">
                <span class="icon"><ion-icon name="document-text-outline"></ion-icon></span>
                <span class="title">Generate Cards</span>
            </a>
            <ul class="dropdown-menu">
                <li class="{{ Request::is('client/stockcard*') && !Request::is('client/stockcard/audit-trail') ? 'active' : '' }}">
                    <a href="{{ route('client.stockcard.index') }}">
                        <span class="icon"><ion-icon name="document-text-outline"></ion-icon></span>
                        <span class="title">Stock Card</span>
                    </a>
                </li>
                <li class="{{ Request::is('client/propertycard*') ? 'active' : '' }}">
                    <a href="{{ route('client.propertycard.index') }}">
                        <span class="icon"><ion-icon name="receipt-outline"></ion-icon></span>
                        <span class="title">Property Card</span>
                    </a>
                </li>
                <li class="{{ Request::is('client/stockcard/audit-trail') ? 'active' : '' }}">
                    <a href="{{ route('client.stockcard.audit-trail') }}">
                        <span class="icon"><ion-icon name="time-outline"></ion-icon></span>
                        <span class="title">Audit Trail</span>
                    </a>
                </li>
            </ul>
        </li>
        
        <li class="{{ Request::is('client/help*') ? 'hovered' : '' }}">
            <a href="{{ route('client.help.index') }}">
                <span class="icon"><ion-icon name="help-outline"></ion-icon></span>
                <span class="title">Help</span>
            </a>
        </li>
        <li class="{{ Request::is('client/profile*') ? 'hovered' : '' }}">
            <a href="{{ route('client.profile.index') }}">
                <span class="icon"><ion-icon name="settings-outline"></ion-icon></span>
                <span class="title">Profile Settings</span>
            </a>
        </li>
        <li class="{{ Request::is('client/about*') ? 'hovered' : '' }}">
            <a href="{{ route('client.about.index') }}">
                <span class="icon"><ion-icon name="information-circle-outline"></ion-icon></span>
                <span class="title">About</span>
            </a>
        </li>
        <li>
            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <span class="icon"><ion-icon name="log-out-outline"></ion-icon></span>
                <span class="title">Sign Out</span>
            </a>
        </li>
    </ul>
</div>

<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>

<script>
function toggleDropdown(event, element) {
    // Prevent the link from navigating
    event.preventDefault();

    // Get the parent li element
    const parentLi = element.parentElement;

    // Remove hovered class from all li elements
    const allLis = document.querySelectorAll('.navigation ul li');
    allLis.forEach(li => {
        li.classList.remove('hovered');
    });

    // Close all other dropdowns first
    const allDropdowns = document.querySelectorAll('.navigation .dropdown');
    allDropdowns.forEach(dropdown => {
        if (dropdown !== parentLi) {
            dropdown.classList.remove('open');
            dropdown.classList.remove('active');
        }
    });

    // Toggle the current dropdown
    parentLi.classList.toggle('open');

    // Add hovered class to the parent li when open
    if (parentLi.classList.contains('open')) {
        parentLi.classList.add('hovered');
    }

    // Add active class when opened or if any child is active
    if (parentLi.classList.contains('open') || parentLi.querySelector('.dropdown-menu li.active')) {
        parentLi.classList.add('active');
    } else {
        parentLi.classList.remove('active');
    }

    // Add hovered class to active child items
    const activeChildren = parentLi.querySelectorAll('.dropdown-menu li.active');
    activeChildren.forEach(child => {
        child.classList.add('hovered');
    });
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const navigation = document.querySelector('.navigation');
    const isClickInsideNav = navigation.contains(event.target);
    
    if (!isClickInsideNav) {
        const allDropdowns = document.querySelectorAll('.navigation .dropdown');
        allDropdowns.forEach(dropdown => {
            dropdown.classList.remove('open');
        });
    }
});

// Ensure dropdown menu items are clickable
document.addEventListener('DOMContentLoaded', function() {
    const dropdownLinks = document.querySelectorAll('.dropdown-menu a');
    dropdownLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            // Ensure the click event works properly
            e.stopPropagation();
            // Allow normal navigation
            window.location.href = this.href;
        });
    });
});
</script>
