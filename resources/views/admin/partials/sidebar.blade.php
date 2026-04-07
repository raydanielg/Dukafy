<aside class="admin-sidebar">
    <div class="admin-sidebar-top">
        <a href="{{ route('admin.dashboard') }}" class="admin-brand">
            <img src="{{ asset('freepik__letter_dukafy_word_make_it_cool_with_green_co.png') }}" alt="Dukafy" class="admin-brand-logo">
            <div class="admin-brand-meta">
                <div class="admin-brand-name">Dukafy</div>
                <div class="admin-brand-sub">ADMIN PANEL</div>
            </div>
        </a>
    </div>

    <nav class="admin-nav">
        <div class="admin-nav-group">
            <div class="admin-nav-title">MAIN</div>
            <a class="admin-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="admin-nav-icon"><path d="M3 10.5 12 3l9 7.5V21a1 1 0 0 1-1 1h-5v-7H9v7H4a1 1 0 0 1-1-1V10.5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>
                <span>Dashboard</span>
            </a>
        </div>

        <div class="admin-nav-group">
            <div class="admin-nav-title">CONTENT</div>
            <a class="admin-nav-link {{ request()->routeIs('admin.articles.*') ? 'active' : '' }}" href="{{ route('admin.articles.index') }}">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="admin-nav-icon"><path d="M7 4h10a2 2 0 0 1 2 2v14l-4-2-3 2-3-2-4 2V6a2 2 0 0 1 2-2Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M9 8h6M9 12h6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                <span>Articles</span>
            </a>
        </div>

        <div class="admin-nav-group">
            <div class="admin-nav-title">ACCOUNT</div>
            <a class="admin-nav-link" href="{{ route('home') }}">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="admin-nav-icon"><path d="M4 21v-2a4 4 0 0 1 4-4h8a4 4 0 0 1 4 4v2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <span>User Dashboard</span>
            </a>
            <a class="admin-nav-link" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('admin-logout-form').submit();">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="admin-nav-icon"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M16 17l5-5-5-5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M21 12H9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <span>Sign Out</span>
            </a>
            <form id="admin-logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </div>
    </nav>
</aside>
