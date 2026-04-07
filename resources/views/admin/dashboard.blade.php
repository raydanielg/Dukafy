@extends('admin.layouts.app')

@section('page_title', 'Super Admin Dashboard')

@section('content')
<div class="admin-dashboard">
    <div class="admin-metrics">
        <div class="admin-metric-card">
            <div class="admin-metric-label">Total Businesses</div>
            <div class="admin-metric-value text-primary">{{ number_format($stats['total_businesses']) }}</div>
        </div>
        <div class="admin-metric-card">
            <div class="admin-metric-label">Active Subscriptions</div>
            <div class="admin-metric-value text-success">{{ number_format($stats['active_subscriptions']) }}</div>
        </div>
        <div class="admin-metric-card">
            <div class="admin-metric-label">Total Platform Revenue</div>
            <div class="admin-metric-value text-info">TZS {{ number_format($stats['total_revenue'], 2) }}</div>
        </div>
        <div class="admin-metric-card">
            <div class="admin-metric-label">Pending Approvals</div>
            <div class="admin-metric-value text-warning">{{ number_format($stats['pending_approvals']) }}</div>
        </div>
    </div>

    <div class="admin-grid mt-4">
        <div class="admin-panel">
            <div class="admin-panel-head">
                <div class="admin-panel-title">Recent Businesses</div>
                <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="admin-panel-body">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Currency</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($stats['recent_businesses'] as $biz)
                                <tr>
                                    <td class="fw-bold">{{ $biz->name }}</td>
                                    <td>{{ $biz->currency }}</td>
                                    <td class="text-muted small">{{ \Carbon\Carbon::parse($biz->created_at)->diffForHumans() }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center py-3 text-muted">No businesses yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="admin-panel">
            <div class="admin-panel-head">
                <div class="admin-panel-title">Recent Subscription Payments</div>
                <a href="{{ route('admin.subscription.billing') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="admin-panel-body">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Amount</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($stats['recent_payments'] as $pay)
                                <tr>
                                    <td>{{ $pay->user_name }}</td>
                                    <td class="fw-bold text-success">{{ number_format($pay->amount, 2) }}</td>
                                    <td class="text-muted small">{{ \Carbon\Carbon::parse($pay->paid_at)->format('M d, Y') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center py-3 text-muted">No payments yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="admin-grid mt-4">
        <div class="admin-panel">
            <div class="admin-panel-head">
                <div class="admin-panel-title">Quick Platform Actions</div>
            </div>
            <div class="admin-panel-body">
                <div class="admin-actions">
                    <a href="{{ route('admin.subscription.plans.create') }}" class="admin-action-btn">Create New Plan</a>
                    <a href="{{ route('admin.articles.create') }}" class="admin-action-btn admin-action-btn-ghost">Add Announcement</a>
                    <form method="POST" action="{{ route('admin.system_settings.clear_cache') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="admin-action-btn admin-action-btn-danger">Clear System Cache</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .admin-action-btn-danger {
        background: #fee2e2;
        color: #ef4444;
        border: 1px solid #fecaca;
    }
    .admin-action-btn-danger:hover {
        background: #ef4444;
        color: white;
    }
</style>
@endsection
