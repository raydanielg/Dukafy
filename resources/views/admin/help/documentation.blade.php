@extends('admin.layouts.app')

@section('page_title', 'Documentation')

@section('content')
<div class="admin-page">
    <div class="admin-page-head">
        <div class="admin-page-title">Knowledge Base & Documentation</div>
    </div>

    <div class="row g-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm sticky-top" style="top: 80px;">
                <div class="list-group list-group-flush rounded-3">
                    <a href="#getting-started" class="list-group-item list-group-item-action fw-bold">🚀 Getting Started</a>
                    <a href="#saas-setup" class="list-group-item list-group-item-action fw-bold">🏢 SaaS Multi-tenancy</a>
                    <a href="#subscriptions" class="list-group-item list-group-item-action fw-bold">💳 Subscriptions</a>
                    <a href="#reports" class="list-group-item list-group-item-action fw-bold">📊 Analytics & Reports</a>
                    <a href="#api" class="list-group-item list-group-item-action fw-bold">🔌 API Integration</a>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="admin-panel mb-4" id="getting-started">
                <div class="admin-panel-head">
                    <div class="admin-panel-title">🚀 Getting Started</div>
                </div>
                <div class="admin-panel-body">
                    <h5>Welcome to Dukafy Admin Panel</h5>
                    <p>Dukafy is a complete SaaS solution for business management. As a Super Admin, you have full control over the platform, while business owners manage their own data.</p>
                    <ul>
                        <li><strong>Dashboard:</strong> Overview of platform performance.</li>
                        <li><strong>User Management:</strong> Manage all platform users and businesses.</li>
                        <li><strong>Finance:</strong> Track global revenue and invoices.</li>
                    </ul>
                </div>
            </div>

            <div class="admin-panel mb-4" id="saas-setup">
                <div class="admin-panel-head">
                    <div class="admin-panel-title">🏢 SaaS Multi-tenancy</div>
                </div>
                <div class="admin-panel-body">
                    <h5>How Multi-tenancy works</h5>
                    <p>Every business in Dukafy is a separate "Tenant". Data is isolated using <code>business_id</code>.</p>
                    <div class="alert alert-info">
                        <strong>Developer Note:</strong> When adding new tables, always include a <code>business_id</code> column to ensure SaaS isolation.
                    </div>
                </div>
            </div>

            <div class="admin-panel mb-4" id="subscriptions">
                <div class="admin-panel-head">
                    <div class="admin-panel-title">💳 Subscriptions</div>
                </div>
                <div class="admin-panel-body">
                    <h5>Managing Plans</h5>
                    <p>You can create multiple subscription plans (Free, Basic, Premium) with different features and limits.</p>
                    <p>Subscriptions are tracked automatically, and businesses are restricted based on their active plan status.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
