@extends('admin.layouts.app')

@section('page_title', 'Dashboard')

@section('content')
<div class="admin-dashboard">
    <div class="admin-metrics">
        <div class="admin-metric-card">
            <div class="admin-metric-label">Users</div>
            <div class="admin-metric-value">--</div>
        </div>
        <div class="admin-metric-card">
            <div class="admin-metric-label">Articles</div>
            <div class="admin-metric-value">--</div>
        </div>
        <div class="admin-metric-card">
            <div class="admin-metric-label">Newsletters</div>
            <div class="admin-metric-value">--</div>
        </div>
        <div class="admin-metric-card">
            <div class="admin-metric-label">Latest Activity</div>
            <div class="admin-metric-value">--</div>
        </div>
    </div>

    <div class="admin-grid">
        <div class="admin-panel">
            <div class="admin-panel-head">
                <div class="admin-panel-title">Quick Actions</div>
            </div>
            <div class="admin-panel-body">
                <div class="admin-actions">
                    <a href="{{ route('admin.articles.create') }}" class="admin-action-btn">Create Article</a>
                    <a href="{{ route('admin.articles.index') }}" class="admin-action-btn admin-action-btn-ghost">Manage Articles</a>
                </div>
            </div>
        </div>

        <div class="admin-panel">
            <div class="admin-panel-head">
                <div class="admin-panel-title">System</div>
            </div>
            <div class="admin-panel-body">
                <div class="admin-empty">No data yet.</div>
            </div>
        </div>
    </div>
</div>
@endsection
