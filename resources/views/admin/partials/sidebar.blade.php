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
        @php
            $openContent = request()->routeIs('admin.articles.*');
        @endphp

        <div class="admin-nav-group">
            <div class="admin-nav-title">HOME</div>
            <a class="admin-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="admin-nav-icon"><path d="M3 10.5 12 3l9 7.5V21a1 1 0 0 1-1 1h-5v-7H9v7H4a1 1 0 0 1-1-1V10.5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>
                <span>Dashboard</span>
            </a>
        </div>

        <div class="admin-nav-group">
            <div class="admin-nav-title">USER MANAGEMENT</div>
            <div class="admin-menu" data-admin-menu>
                <button class="admin-nav-link admin-nav-link-toggle" type="button" aria-expanded="false">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="admin-nav-icon"><path d="M4 21v-2a4 4 0 0 1 4-4h8a4 4 0 0 1 4 4v2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <span>Users</span>
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="admin-nav-caret"><path d="m9 18 6-6-6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
                <div class="admin-submenu" data-admin-submenu>
                    <a class="admin-sub-link {{ request()->routeIs('admin.users.index') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">All Users</a>
                    <a class="admin-sub-link {{ request()->routeIs('admin.users.create') ? 'active' : '' }}" href="{{ route('admin.users.create') }}">Add New User</a>
                    <a class="admin-sub-link" href="#">User Roles</a>
                    <a class="admin-sub-link" href="#">Role Permissions</a>
                    <a class="admin-sub-link {{ request()->routeIs('admin.users.banned') ? 'active' : '' }}" href="{{ route('admin.users.banned') }}">Banned Users</a>
                    <a class="admin-sub-link {{ request()->routeIs('admin.users.pending') ? 'active' : '' }}" href="{{ route('admin.users.pending') }}">Pending Approvals</a>
                    <a class="admin-sub-link" href="#">User Activity Log</a>
                    <a class="admin-sub-link" href="#">User Groups</a>
                    <a class="admin-sub-link" href="#">Login History</a>
                    <a class="admin-sub-link" href="#">Profile Management</a>
                </div>
            </div>
        </div>

        <div class="admin-nav-group">
            <div class="admin-nav-title">SUBSCRIPTION</div>
            <div class="admin-menu" data-admin-menu>
                <button class="admin-nav-link admin-nav-link-toggle" type="button" aria-expanded="false">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="admin-nav-icon"><path d="M12 1v22" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7H14a3.5 3.5 0 0 1 0 7H6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <span>Plans & Billing</span>
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="admin-nav-caret"><path d="m9 18 6-6-6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
                <div class="admin-submenu" data-admin-submenu>
                    <a class="admin-sub-link" href="#">All Subscriptions</a>
                    <a class="admin-sub-link" href="#">Plans</a>
                    <a class="admin-sub-link" href="#">Add New Plan</a>
                    <a class="admin-sub-link" href="#">Edit Plan</a>
                    <a class="admin-sub-link" href="#">Assign Plan to User</a>
                    <a class="admin-sub-link" href="#">Subscription History</a>
                    <a class="admin-sub-link" href="#">Expiring Soon</a>
                    <a class="admin-sub-link" href="#">Cancelled Subscriptions</a>
                    <a class="admin-sub-link" href="#">Trial Requests</a>
                    <a class="admin-sub-link" href="#">Invoices & Payments</a>
                </div>
            </div>
        </div>

        <div class="admin-nav-group">
            <div class="admin-nav-title">SECURITY</div>
            <div class="admin-menu" data-admin-menu>
                <button class="admin-nav-link admin-nav-link-toggle" type="button" aria-expanded="false">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="admin-nav-icon"><path d="M12 1 3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M9.5 12 11 13.5 14.5 10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <span>Security</span>
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="admin-nav-caret"><path d="m9 18 6-6-6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
                <div class="admin-submenu" data-admin-submenu>
                    <a class="admin-sub-link" href="#">Login Security</a>
                    <a class="admin-sub-link" href="#">Password Policy</a>
                    <a class="admin-sub-link" href="#">IP Whitelisting</a>
                    <a class="admin-sub-link" href="#">Blocked IPs</a>
                    <a class="admin-sub-link" href="#">Database Encryption</a>
                    <a class="admin-sub-link" href="#">Backup Security</a>
                    <a class="admin-sub-link" href="#">Audit Log</a>
                    <a class="admin-sub-link" href="#">Session Management</a>
                    <a class="admin-sub-link" href="#">API Security</a>
                    <a class="admin-sub-link" href="#">Data Retention Policy</a>
                    <a class="admin-sub-link" href="#">Security Alerts</a>
                </div>
            </div>
        </div>

        <div class="admin-nav-group">
            <div class="admin-nav-title">CONTENT</div>
            <div class="admin-menu {{ $openContent ? 'is-open' : '' }}" data-admin-menu>
                <button class="admin-nav-link admin-nav-link-toggle {{ $openContent ? 'active' : '' }}" type="button" aria-expanded="{{ $openContent ? 'true' : 'false' }}">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="admin-nav-icon"><path d="M7 4h10a2 2 0 0 1 2 2v14l-4-2-3 2-3-2-4 2V6a2 2 0 0 1 2-2Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M9 8h6M9 12h6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                    <span>Articles</span>
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="admin-nav-caret"><path d="m9 18 6-6-6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
                <div class="admin-submenu" data-admin-submenu>
                    <a class="admin-sub-link {{ request()->routeIs('admin.articles.index') ? 'active' : '' }}" href="{{ route('admin.articles.index') }}">All Articles</a>
                    <a class="admin-sub-link {{ request()->routeIs('admin.articles.create') ? 'active' : '' }}" href="{{ route('admin.articles.create') }}">Add New Article</a>
                    <a class="admin-sub-link" href="#">Categories</a>
                </div>
            </div>
        </div>

        <div class="admin-nav-group">
            <div class="admin-nav-title">FINANCE</div>
            <div class="admin-menu" data-admin-menu>
                <button class="admin-nav-link admin-nav-link-toggle" type="button" aria-expanded="false">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="admin-nav-icon"><path d="M12 1v22" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M6 7h9a3 3 0 0 1 0 6H9a3 3 0 0 0 0 6h9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <span>Finance & Billing</span>
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="admin-nav-caret"><path d="m9 18 6-6-6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
                <div class="admin-submenu" data-admin-submenu>
                    <a class="admin-sub-link" href="#">Revenue Overview</a>
                    <a class="admin-sub-link" href="#">Payment Methods</a>
                    <a class="admin-sub-link" href="#">Payment Gateway</a>
                    <a class="admin-sub-link" href="#">Invoice Settings</a>
                    <a class="admin-sub-link" href="#">Tax Settings</a>
                    <a class="admin-sub-link" href="#">Expenses</a>
                    <a class="admin-sub-link" href="#">Profit & Loss</a>
                </div>
            </div>
        </div>

        <div class="admin-nav-group">
            <div class="admin-nav-title">OPERATIONS</div>
            <div class="admin-menu" data-admin-menu>
                <button class="admin-nav-link admin-nav-link-toggle" type="button" aria-expanded="false">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="admin-nav-icon"><path d="M20 7H4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M10 11H4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M20 11h-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M20 15H4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M8 19H4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M20 19h-8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                    <span>Business Data</span>
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="admin-nav-caret"><path d="m9 18 6-6-6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
                <div class="admin-submenu" data-admin-submenu>
                    <a class="admin-sub-link" href="#">Products</a>
                    <a class="admin-sub-link" href="#">Product Categories</a>
                    <a class="admin-sub-link" href="#">Low Stock Alerts</a>
                    <a class="admin-sub-link" href="#">Bulk Import/Export</a>
                    <a class="admin-sub-link" href="#">Sales</a>
                    <a class="admin-sub-link" href="#">Sales by Business</a>
                    <a class="admin-sub-link" href="#">Sales by User</a>
                    <a class="admin-sub-link" href="#">Customers</a>
                    <a class="admin-sub-link" href="#">Customer Groups</a>
                    <a class="admin-sub-link" href="#">Blacklisted Customers</a>
                </div>
            </div>
        </div>

        <div class="admin-nav-group">
            <div class="admin-nav-title">REPORTS</div>
            <div class="admin-menu" data-admin-menu>
                <button class="admin-nav-link admin-nav-link-toggle" type="button" aria-expanded="false">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="admin-nav-icon"><path d="M4 19V5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M4 19h16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M8 15v-4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M12 15V7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M16 15v-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                    <span>Reports</span>
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="admin-nav-caret"><path d="m9 18 6-6-6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
                <div class="admin-submenu" data-admin-submenu>
                    <a class="admin-sub-link" href="#">System Usage</a>
                    <a class="admin-sub-link" href="#">Business Performance</a>
                    <a class="admin-sub-link" href="#">Subscription Revenue</a>
                    <a class="admin-sub-link" href="#">Churn Report</a>
                </div>
            </div>
        </div>

        <div class="admin-nav-group">
            <div class="admin-nav-title">SETTINGS</div>
            <div class="admin-menu" data-admin-menu>
                <button class="admin-nav-link admin-nav-link-toggle" type="button" aria-expanded="false">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="admin-nav-icon"><path d="M12 15.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Z" stroke="currentColor" stroke-width="1.8"/><path d="M19.4 15a1.7 1.7 0 0 0 .34 1.87l.05.06a2.06 2.06 0 0 1-1.46 3.5 2.06 2.06 0 0 1-1.45-.6l-.06-.05a1.7 1.7 0 0 0-1.87-.34 1.7 1.7 0 0 0-1 1.55V21a2.06 2.06 0 0 1-4.12 0v-.08a1.7 1.7 0 0 0-1-1.55 1.7 1.7 0 0 0-1.87.34l-.06.05a2.06 2.06 0 1 1-2.91-2.91l.05-.06a1.7 1.7 0 0 0 .34-1.87 1.7 1.7 0 0 0-1.55-1H3a2.06 2.06 0 0 1 0-4.12h.08a1.7 1.7 0 0 0 1.55-1 1.7 1.7 0 0 0-.34-1.87l-.05-.06a2.06 2.06 0 1 1 2.91-2.91l.06.05a1.7 1.7 0 0 0 1.87.34 1.7 1.7 0 0 0 1-1.55V3a2.06 2.06 0 0 1 4.12 0v.08a1.7 1.7 0 0 0 1 1.55 1.7 1.7 0 0 0 1.87-.34l.06-.05a2.06 2.06 0 1 1 2.91 2.91l-.05.06a1.7 1.7 0 0 0-.34 1.87 1.7 1.7 0 0 0 1.55 1H21a2.06 2.06 0 0 1 0 4.12h-.08a1.7 1.7 0 0 0-1.55 1Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>
                    <span>System Settings</span>
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="admin-nav-caret"><path d="m9 18 6-6-6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
                <div class="admin-submenu" data-admin-submenu>
                    <a class="admin-sub-link" href="#">General Settings</a>
                    <a class="admin-sub-link" href="#">Business Defaults</a>
                    <a class="admin-sub-link" href="#">Email Settings</a>
                    <a class="admin-sub-link" href="#">SMS / WhatsApp Settings</a>
                    <a class="admin-sub-link" href="#">Backup Settings</a>
                    <a class="admin-sub-link" href="#">Maintenance Mode</a>
                    <a class="admin-sub-link" href="#">Clear Cache</a>
                    <a class="admin-sub-link" href="#">System Health</a>
                </div>
            </div>
        </div>

        <div class="admin-nav-group">
            <div class="admin-nav-title">EXTRAS</div>
            <div class="admin-menu" data-admin-menu>
                <button class="admin-nav-link admin-nav-link-toggle" type="button" aria-expanded="false">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="admin-nav-icon"><path d="M8 6h13M8 12h13M8 18h13" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M3 6h.01M3 12h.01M3 18h.01" stroke="currentColor" stroke-width="3" stroke-linecap="round"/></svg>
                    <span>Extras</span>
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="admin-nav-caret"><path d="m9 18 6-6-6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
                <div class="admin-submenu" data-admin-submenu>
                    <a class="admin-sub-link" href="#">Modules Manager</a>
                    <a class="admin-sub-link" href="#">Announcements</a>
                    <a class="admin-sub-link" href="#">System Logs</a>
                    <a class="admin-sub-link" href="#">Cron Jobs</a>
                </div>
            </div>
        </div>

        <div class="admin-nav-group">
            <div class="admin-nav-title">LOGS</div>
            <div class="admin-menu" data-admin-menu>
                <button class="admin-nav-link admin-nav-link-toggle" type="button" aria-expanded="false">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="admin-nav-icon"><path d="M14 2H6a2 2 0 0 0-2 2v16l4-2 4 2 4-2 4 2V8l-6-6Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M14 2v6h6" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M8 12h8M8 16h8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                    <span>Logs</span>
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="admin-nav-caret"><path d="m9 18 6-6-6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
                <div class="admin-submenu" data-admin-submenu>
                    <a class="admin-sub-link" href="#">Access Logs</a>
                    <a class="admin-sub-link" href="#">Error Logs</a>
                    <a class="admin-sub-link" href="#">Payment Logs</a>
                    <a class="admin-sub-link" href="#">Email Logs</a>
                </div>
            </div>
        </div>

        <div class="admin-nav-group">
            <div class="admin-nav-title">HELP</div>
            <div class="admin-menu" data-admin-menu>
                <button class="admin-nav-link admin-nav-link-toggle" type="button" aria-expanded="false">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="admin-nav-icon"><path d="M12 22a10 10 0 1 0 0-20 10 10 0 0 0 0 20Z" stroke="currentColor" stroke-width="1.8"/><path d="M9.1 9a3 3 0 1 1 5.8 1c-.55 1.1-1.9 1.4-1.9 2.9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M12 17h.01" stroke="currentColor" stroke-width="3" stroke-linecap="round"/></svg>
                    <span>Help & Support</span>
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="admin-nav-caret"><path d="m9 18 6-6-6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
                <div class="admin-submenu" data-admin-submenu>
                    <a class="admin-sub-link" href="#">Documentation</a>
                    <a class="admin-sub-link" href="#">Support Tickets</a>
                    <a class="admin-sub-link" href="#">System Info</a>
                    <a class="admin-sub-link" href="#">Contact Developer</a>
                </div>
            </div>
        </div>

        <div class="admin-nav-group">
            <div class="admin-nav-title">ACCOUNT</div>
            <a class="admin-nav-link" href="{{ route('home') }}">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="admin-nav-icon"><path d="M12 3v18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M7 7h10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M7 17h10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
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
