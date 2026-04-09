<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard - {{ config('app.name', 'Dukafy') }}</title>
    <link rel="icon" href="{{ asset('freepik__letter_dukafy_word_make_it_cool_with_green_co.png') }}">
    <link href="https://fonts.bunny.net/css?family=Nunito:400,600,700" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    @if(app()->environment('production'))
        <link rel="stylesheet" href="{{ asset('build/assets/app-CYWMYAbo.css') }}">
        <script src="{{ asset('build/assets/app-BX6Qa1eb.js') }}" defer></script>
    @else
        @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @endif
    <style>
        :root {
            --user-primary: #2563eb;
            --user-sidebar: #ffffff;
            --user-bg: #f8fafc;
            --user-text: #1e293b;
            --user-border: #e2e8f0;
        }
        body { background: var(--user-bg); font-family: 'Nunito', sans-serif; color: var(--user-text); }
        .user-shell { display: flex; min-height: 100vh; }
        .user-sidebar { width: 260px; background: var(--user-sidebar); border-right: 1px solid var(--user-border); display: flex; flex-direction: column; position: fixed; height: 100vh; }
        .user-main { flex: 1; margin-left: 260px; display: flex; flex-direction: column; min-width: 0; }
        .user-sidebar-top { padding: 20px; border-bottom: 1px solid var(--user-border); text-align: center; }
        .user-avatar { width: 60px; height: 60px; border-radius: 50%; background: #e2e8f0; margin: 0 auto 10px; display: flex; align-items: center; justify-content: center; font-size: 24px; color: var(--user-primary); font-weight: bold; border: 2px solid var(--user-primary); }
        .user-name { font-weight: 700; font-size: 16px; margin-bottom: 2px; }
        .user-id { font-size: 12px; color: #64748b; }
        .user-nav { flex: 1; padding: 15px 0; overflow-y: auto; }
        .user-nav-link { display: flex; align-items: center; padding: 10px 20px; color: #475569; text-decoration: none; font-size: 14px; transition: all 0.2s; }
        .user-nav-link i { width: 24px; font-size: 18px; margin-right: 12px; color: #94a3b8; }
        .user-nav-link:hover { background: #f1f5f9; color: var(--user-primary); }
        .user-nav-link.active { background: #eff6ff; color: var(--user-primary); border-right: 3px solid var(--user-primary); font-weight: 600; }
        .user-nav-link.active i { color: var(--user-primary); }
        .user-nav-badge { margin-left: auto; background: #f1f5f9; padding: 2px 8px; border-radius: 10px; font-size: 11px; color: #64748b; }
        .user-header { height: 60px; background: #fff; border-bottom: 1px solid var(--user-border); display: flex; align-items: center; padding: 0 25px; position: sticky; top: 0; z-index: 10; }
        .user-content { padding: 25px; }
        .user-logout { margin-top: auto; padding: 15px 20px; border-top: 1px solid var(--user-border); }
        .btn-logout { display: flex; align-items: center; color: #ef4444; text-decoration: none; font-size: 14px; font-weight: 600; }
        .btn-logout i { margin-right: 10px; }
    </style>
    @yield('styles')
</head>
<body>
    <div class="user-shell">
        <aside class="user-sidebar">
            <div class="user-sidebar-top">
                <div class="user-avatar">{{ substr(Auth::user()->name, 0, 1) }}</div>
                <div class="user-name">{{ Auth::user()->name }}</div>
                <div class="user-id">User ID: U{{ Auth::user()->id + 2000 }}</div>
            </div>
            
            <nav class="user-nav">
                <a href="{{ route('home') }}" class="user-nav-link {{ request()->routeIs('home') ? 'active' : '' }}">
                    <i class="fa-solid fa-house"></i>
                    <span>Dashboard</span>
                </a>
                <a href="#" class="user-nav-link">
                    <i class="fa-solid fa-shop"></i>
                    <span>Shops</span>
                    <span class="user-nav-badge">{{ $stats['shops_count'] ?? 0 }}</span>
                </a>
                <a href="#" class="user-nav-link">
                    <i class="fa-solid fa-users-gear"></i>
                    <span>Members</span>
                    <span class="user-nav-badge">{{ $stats['members_count'] ?? 0 }}</span>
                </a>
                <a href="#" class="user-nav-link">
                    <i class="fa-solid fa-user-group"></i>
                    <span>Customers</span>
                    <span class="user-nav-badge">{{ $stats['customers_count'] ?? 0 }}</span>
                </a>
                <a href="#" class="user-nav-link">
                    <i class="fa-solid fa-truck-field"></i>
                    <span>Suppliers</span>
                    <span class="user-nav-badge">0</span>
                </a>
                <a href="#" class="user-nav-link">
                    <i class="fa-solid fa-box"></i>
                    <span>Products</span>
                    <span class="user-nav-badge">{{ $stats['products_count'] ?? 0 }}</span>
                </a>
                <a href="#" class="user-nav-link">
                    <i class="fa-solid fa-share-nodes"></i>
                    <span>Share</span>
                </a>
            </nav>

            <div class="user-logout">
                <a href="{{ route('logout') }}" class="btn-logout" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    <span>Logout</span>
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
            </div>
        </aside>

        <main class="user-main">
            <header class="user-header">
                <div class="fw-bold text-primary">Dukafy <span class="text-muted fw-normal ms-2">/ User Panel</span></div>
                <div class="ms-auto d-flex align-items-center">
                    <button class="btn btn-sm btn-light border rounded-pill px-3 me-2">
                        <i class="fa-solid fa-plus me-1 text-primary"></i> New
                    </button>
                    <div class="dropdown">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=0D8ABC&color=fff" class="rounded-circle" width="32" height="32" style="cursor:pointer">
                    </div>
                </div>
            </header>

            <div class="user-content">
                @yield('content')
            </div>
        </main>
    </div>
    @yield('scripts')
</body>
</html>
