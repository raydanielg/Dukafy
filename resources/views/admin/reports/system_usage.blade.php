@extends('admin.layouts.app')

@section('page_title', 'System Usage Report')

@section('content')
<div class="admin-page">
    <div class="admin-page-head">
        <div class="admin-page-title">System Usage</div>
    </div>

    <div class="admin-metrics mb-4">
        <div class="admin-metric-card">
            <div class="admin-metric-label">Total Users</div>
            <div class="admin-metric-value">{{ number_format($stats['total_users']) }}</div>
        </div>
        <div class="admin-metric-card">
            <div class="admin-metric-label">New Users (30d)</div>
            <div class="admin-metric-value">{{ number_format($stats['new_users_30d']) }}</div>
        </div>
        <div class="admin-metric-card">
            <div class="admin-metric-label">Articles</div>
            <div class="admin-metric-value">{{ number_format($stats['total_articles']) }}</div>
        </div>
        <div class="admin-metric-card">
            <div class="admin-metric-label">Newsletters</div>
            <div class="admin-metric-value">{{ number_format($stats['total_newsletters']) }}</div>
        </div>
    </div>

    <div class="admin-grid">
        <div class="admin-panel">
            <div class="admin-panel-head">
                <div class="admin-panel-title">User Registrations (Last 30 Days)</div>
            </div>
            <div class="admin-panel-body">
                <canvas id="userTrendChart" height="200"></canvas>
            </div>
        </div>

        <div class="admin-panel">
            <div class="admin-panel-head">
                <div class="admin-panel-title">Login Activity (Last 30 Days)</div>
            </div>
            <div class="admin-panel-body">
                <canvas id="loginTrendChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const userTrendCtx = document.getElementById('userTrendChart').getContext('2d');
    new Chart(userTrendCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($userTrend->pluck('date')) !!},
            datasets: [{
                label: 'New Registrations',
                data: {!! json_encode($userTrend->pluck('count')) !!},
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    });

    const loginTrendCtx = document.getElementById('loginTrendChart').getContext('2d');
    new Chart(loginTrendCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($loginTrend->pluck('date')) !!},
            datasets: [{
                label: 'Logins',
                data: {!! json_encode($loginTrend->pluck('count')) !!},
                backgroundColor: '#3b82f6',
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    });
</script>
@endsection
