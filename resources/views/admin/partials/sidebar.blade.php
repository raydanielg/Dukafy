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
                    <a class="admin-sub-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}" href="{{ route('admin.roles.index') }}">User Roles</a>
                    <a class="admin-sub-link {{ request()->routeIs('admin.permissions.*') ? 'active' : '' }}" href="{{ route('admin.permissions.index') }}">Role Permissions</a>
                    <a class="admin-sub-link {{ request()->routeIs('admin.users.banned') ? 'active' : '' }}" href="{{ route('admin.users.banned') }}">Banned Users</a>
                    <a class="admin-sub-link {{ request()->routeIs('admin.users.pending') ? 'active' : '' }}" href="{{ route('admin.users.pending') }}">Pending Approvals</a>
                    <a class="admin-sub-link {{ request()->routeIs('admin.activity.index') ? 'active' : '' }}" href="{{ route('admin.activity.index') }}">User Activity Log</a>
                    <a class="admin-sub-link {{ request()->routeIs('admin.groups.*') ? 'active' : '' }}" href="{{ route('admin.groups.index') }}">User Groups</a>
                    <a class="admin-sub-link {{ request()->routeIs('admin.login_history.index') ? 'active' : '' }}" href="{{ route('admin.login_history.index') }}">Login History</a>
                    <a class="admin-sub-link {{ request()->routeIs('admin.profile.*') ? 'active' : '' }}" href="{{ route('admin.profile.index') }}">Profile Management</a>
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
                    <a class="admin-sub-link {{ request()->routeIs('admin.subscription.subscriptions') ? 'active' : '' }}" href="{{ route('admin.subscription.subscriptions') }}">All Subscriptions</a>
                    <a class="admin-sub-link {{ request()->routeIs('admin.subscription.plans') ? 'active' : '' }}" href="{{ route('admin.subscription.plans') }}">Plans</a>
                    <a class="admin-sub-link {{ request()->routeIs('admin.subscription.plans.create') ? 'active' : '' }}" href="{{ route('admin.subscription.plans.create') }}">Add New Plan</a>
                    <a class="admin-sub-link" href="{{ route('admin.subscription.assign') }}">Assign Plan to User</a>
                    <a class="admin-sub-link {{ request()->routeIs('admin.subscription.history') ? 'active' : '' }}" href="{{ route('admin.subscription.history') }}">Subscription History</a>
                    <a class="admin-sub-link {{ request()->routeIs('admin.subscription.expiring') ? 'active' : '' }}" href="{{ route('admin.subscription.expiring') }}">Expiring Soon</a>
                    <a class="admin-sub-link {{ request()->routeIs('admin.subscription.cancelled') ? 'active' : '' }}" href="{{ route('admin.subscription.cancelled') }}">Cancelled Subscriptions</a>
                    <a class="admin-sub-link {{ request()->routeIs('admin.subscription.trials') ? 'active' : '' }}" href="{{ route('admin.subscription.trials') }}">Trial Requests</a>
                    <a class="admin-sub-link {{ request()->routeIs('admin.subscription.billing') ? 'active' : '' }}" href="{{ route('admin.subscription.billing') }}">Invoices & Payments</a>
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
                    <a class="admin-sub-link {{ request()->routeIs('admin.security.login_security*') ? 'active' : '' }}" href="{{ route('admin.security.login_security') }}">Login Security</a>
                    <a class="admin-sub-link {{ request()->routeIs('admin.security.password_policy*') ? 'active' : '' }}" href="{{ route('admin.security.password_policy') }}">Password Policy</a>
                    <a class="admin-sub-link {{ request()->routeIs('admin.security.ip_whitelisting*') ? 'active' : '' }}" href="{{ route('admin.security.ip_whitelisting') }}">IP Whitelisting</a>
                    <a class="admin-sub-link {{ request()->routeIs('admin.security.blocked_ips*') ? 'active' : '' }}" href="{{ route('admin.security.blocked_ips') }}">Blocked IPs</a>
                    <a class="admin-sub-link {{ request()->routeIs('admin.security.database_encryption*') ? 'active' : '' }}" href="{{ route('admin.security.database_encryption') }}">Database Encryption</a>
                    <a class="admin-sub-link {{ request()->routeIs('admin.security.backup_security*') ? 'active' : '' }}" href="{{ route('admin.security.backup_security') }}">Backup Security</a>
                    <a class="admin-sub-link {{ request()->routeIs('admin.security.audit_log') ? 'active' : '' }}" href="{{ route('admin.security.audit_log') }}">Audit Log</a>
                    <a class="admin-sub-link {{ request()->routeIs('admin.security.session_management') ? 'active' : '' }}" href="{{ route('admin.security.session_management') }}">Session Management</a>
                    <a class="admin-sub-link {{ request()->routeIs('admin.security.api_security*') ? 'active' : '' }}" href="{{ route('admin.security.api_security') }}">API Security</a>
                    <a class="admin-sub-link {{ request()->routeIs('admin.security.data_retention*') ? 'active' : '' }}" href="{{ route('admin.security.data_retention') }}">Data Retention Policy</a>
                    <a class="admin-sub-link {{ request()->routeIs('admin.security.security_alerts*') ? 'active' : '' }}" href="{{ route('admin.security.security_alerts') }}">Security Alerts</a>
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
                    <a class="admin-sub-link {{ request()->routeIs('admin.article_categories.*') ? 'active' : '' }}" href="{{ route('admin.article_categories.index') }}">Categories</a>
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
                    <a class="admin-sub-link {{ request()->routeIs('admin.finance.revenue_overview') ? 'active' : '' }}" href="{{ route('admin.finance.revenue_overview') }}">Revenue Overview</a>
                    <a class="admin-sub-link {{ request()->routeIs('admin.finance.payment_methods*') ? 'active' : '' }}" href="{{ route('admin.finance.payment_methods') }}">Payment Methods</a>
                    <a class="admin-sub-link {{ request()->routeIs('admin.finance.payment_gateway*') ? 'active' : '' }}" href="{{ route('admin.finance.payment_gateway') }}">Payment Gateway</a>
                    <a class="admin-sub-link {{ request()->routeIs('admin.finance.invoice_settings*') ? 'active' : '' }}" href="{{ route('admin.finance.invoice_settings') }}">Invoice Settings</a>
                    <a class="admin-sub-link {{ request()->routeIs('admin.finance.tax_settings*') ? 'active' : '' }}" href="{{ route('admin.finance.tax_settings') }}">Tax Settings</a>
                    <a class="admin-sub-link {{ request()->routeIs('admin.finance.expenses*') ? 'active' : '' }}" href="{{ route('admin.finance.expenses') }}">Expenses</a>
                    <a class="admin-sub-link {{ request()->routeIs('admin.finance.profit_loss') ? 'active' : '' }}" href="{{ route('admin.finance.profit_loss') }}">Profit & Loss</a>
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
                    <a class="admin-sub-link {{ request()->routeIs('admin.business_data.products.*') ? 'active' : '' }}" href="{{ route('admin.business_data.products.index') }}">Products</a>
                    <a class="admin-sub-link {{ request()->routeIs('admin.business_data.product_categories.*') ? 'active' : '' }}" href="{{ route('admin.business_data.product_categories.index') }}">Product Categories</a>
                    <a class="admin-sub-link {{ request()->routeIs('admin.business_data.low_stock_alerts') ? 'active' : '' }}" href="{{ route('admin.business_data.low_stock_alerts') }}">Low Stock Alerts</a>
                    <a class="admin-sub-link {{ request()->routeIs('admin.business_data.bulk_import_export*') ? 'active' : '' }}" href="{{ route('admin.business_data.bulk_import_export') }}">Bulk Import/Export</a>
                    <a class="admin-sub-link {{ request()->routeIs('admin.business_data.sales.*') ? 'active' : '' }}" href="{{ route('admin.business_data.sales.index') }}">Sales</a>
                    <a class="admin-sub-link {{ request()->routeIs('admin.business_data.sales_by_business') ? 'active' : '' }}" href="{{ route('admin.business_data.sales_by_business') }}">Sales by Business</a>
                    <a class="admin-sub-link {{ request()->routeIs('admin.business_data.sales_by_user') ? 'active' : '' }}" href="{{ route('admin.business_data.sales_by_user') }}">Sales by User</a>
                    <a class="admin-sub-link {{ request()->routeIs('admin.business_data.customers.*') ? 'active' : '' }}" href="{{ route('admin.business_data.customers.index') }}">Customers</a>
                    <a class="admin-sub-link {{ request()->routeIs('admin.business_data.customer_groups.*') ? 'active' : '' }}" href="{{ route('admin.business_data.customer_groups.index') }}">Customer Groups</a>
                    <a class="admin-sub-link {{ request()->routeIs('admin.business_data.blacklisted_customers') ? 'active' : '' }}" href="{{ route('admin.business_data.blacklisted_customers') }}">Blacklisted Customers</a>
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
                    <a class="admin-sub-link {{ request()->routeIs('admin.reports.system_usage') ? 'active' : '' }}" href="{{ route('admin.reports.system_usage') }}">System Usage</a>
                    <a class="admin-sub-link {{ request()->routeIs('admin.reports.business_performance') ? 'active' : '' }}" href="{{ route('admin.reports.business_performance') }}">Business Performance</a>
                    <a class="admin-sub-link {{ request()->routeIs('admin.reports.subscription_revenue') ? 'active' : '' }}" href="{{ route('admin.reports.subscription_revenue') }}">Subscription Revenue</a>
                    <a class="admin-sub-link {{ request()->routeIs('admin.reports.churn_report') ? 'active' : '' }}" href="{{ route('admin.reports.churn_report') }}">Churn Report</a>
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
                    <a class="admin-sub-link {{ request()->routeIs('admin.system_settings.general*') ? 'active' : '' }}" href="{{ route('admin.system_settings.general') }}">General Settings</a>
                    <a class="admin-sub-link {{ request()->routeIs('admin.system_settings.business_defaults*') ? 'active' : '' }}" href="{{ route('admin.system_settings.business_defaults') }}">Business Defaults</a>
                    <a class="admin-sub-link {{ request()->routeIs('admin.system_settings.email*') ? 'active' : '' }}" href="{{ route('admin.system_settings.email') }}">Email Settings</a>
                    <a class="admin-sub-link {{ request()->routeIs('admin.system_settings.sms_whatsapp*') ? 'active' : '' }}" href="{{ route('admin.system_settings.sms_whatsapp') }}">SMS / WhatsApp Settings</a>
                    <a class="admin-sub-link {{ request()->routeIs('admin.system_settings.backup*') ? 'active' : '' }}" href="{{ route('admin.system_settings.backup') }}">Backup Settings</a>
                    <a class="admin-sub-link {{ request()->routeIs('admin.system_settings.maintenance*') ? 'active' : '' }}" href="{{ route('admin.system_settings.maintenance') }}">Maintenance Mode</a>
                    <form method="POST" action="{{ route('admin.system_settings.clear_cache') }}" onsubmit="return confirm('Clear cache now?');" class="d-inline">
                        @csrf
                        <button type="submit" class="admin-sub-link" style="background:none;border:0;padding:0;text-align:left;width:100%;">Clear Cache</button>
                    </form>
                    <a class="admin-sub-link {{ request()->routeIs('admin.system_settings.health') ? 'active' : '' }}" href="{{ route('admin.system_settings.health') }}">System Health</a>
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
                    <a class="admin-sub-link {{ request()->routeIs('admin.extras.modules.*') ? 'active' : '' }}" href="{{ route('admin.extras.modules.index') }}">Modules Manager</a>
                    <a class="admin-sub-link {{ request()->routeIs('admin.extras.announcements.*') ? 'active' : '' }}" href="{{ route('admin.extras.announcements.index') }}">Announcements</a>
                    <a class="admin-sub-link {{ request()->routeIs('admin.extras.system_logs') ? 'active' : '' }}" href="{{ route('admin.extras.system_logs') }}">System Logs</a>
                    <a class="admin-sub-link {{ request()->routeIs('admin.extras.cron_jobs.*') ? 'active' : '' }}" href="{{ route('admin.extras.cron_jobs.index') }}">Cron Jobs</a>
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
                    <a class="admin-sub-link {{ request()->routeIs('admin.logs.access') ? 'active' : '' }}" href="{{ route('admin.logs.access') }}">Access Logs</a>
                    <a class="admin-sub-link {{ request()->routeIs('admin.logs.errors') ? 'active' : '' }}" href="{{ route('admin.logs.errors') }}">Error Logs</a>
                    <a class="admin-sub-link {{ request()->routeIs('admin.logs.payments') ? 'active' : '' }}" href="{{ route('admin.logs.payments') }}">Payment Logs</a>
                    <a class="admin-sub-link {{ request()->routeIs('admin.logs.emails') ? 'active' : '' }}" href="{{ route('admin.logs.emails') }}">Email Logs</a>
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
                    <a class="admin-sub-link {{ request()->routeIs('admin.help.documentation') ? 'active' : '' }}" href="{{ route('admin.help.documentation') }}">Documentation</a>
                    <a class="admin-sub-link {{ request()->routeIs('admin.help.tickets*') ? 'active' : '' }}" href="{{ route('admin.help.tickets') }}">Support Tickets</a>
                    <a class="admin-sub-link {{ request()->routeIs('admin.help.system_info') ? 'active' : '' }}" href="{{ route('admin.help.system_info') }}">System Info</a>
                    <a class="admin-sub-link {{ request()->routeIs('admin.help.contact_developer') ? 'active' : '' }}" href="{{ route('admin.help.contact_developer') }}">Contact Developer</a>
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
