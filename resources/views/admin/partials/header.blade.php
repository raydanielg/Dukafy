<header class="admin-header">
    <div class="admin-header-left">
        <!-- Mobile Menu Toggle -->
        <button type="button" class="admin-icon-btn d-lg-none me-3" id="admin-menu-toggle" aria-label="Toggle Menu">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="admin-icon"><path d="M3 12h18M3 6h18M3 18h18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
        </button>
        <div class="admin-page-title">@yield('page_title', 'Dashboard')</div>
    </div>

    <div class="admin-header-right">
        <button type="button" class="admin-icon-btn" aria-label="Notifications">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="admin-icon"><path d="M15 17H9m8-6a5 5 0 0 0-10 0c0 5-2 6-2 6h14s-2-1-2-6Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M13.73 21a2 2 0 0 1-3.46 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>

        <div class="admin-user">
            <div class="admin-user-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}</div>
            <div class="admin-user-meta">
                <div class="admin-user-name">{{ auth()->user()->name }}</div>
                <div class="admin-user-role">Administrator</div>
            </div>
        </div>
    </div>
</header>
