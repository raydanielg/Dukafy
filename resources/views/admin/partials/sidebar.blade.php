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
                <span>Dashboard</span>
            </a>
        </div>

        <div class="admin-nav-group">
            <div class="admin-nav-title">CONTENT</div>
            <a class="admin-nav-link {{ request()->routeIs('admin.articles.*') ? 'active' : '' }}" href="{{ route('admin.articles.index') }}">
                <span>Articles</span>
            </a>
        </div>

        <div class="admin-nav-group">
            <div class="admin-nav-title">ACCOUNT</div>
            <a class="admin-nav-link" href="{{ route('home') }}">
                <span>User Dashboard</span>
            </a>
            <a class="admin-nav-link" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('admin-logout-form').submit();">
                <span>Sign Out</span>
            </a>
            <form id="admin-logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </div>
    </nav>
</aside>
