@extends('admin.layouts.app')

@section('page_title', 'Churn Report')

@section('content')
<div class="admin-page">
    <div class="admin-page-head">
        <div class="admin-page-title">Churn Report</div>
    </div>

    <div class="admin-metrics mb-4">
        <div class="admin-metric-card">
            <div class="admin-metric-label">Active</div>
            <div class="admin-metric-value text-success">{{ number_format($stats['active']) }}</div>
        </div>
        <div class="admin-metric-card">
            <div class="admin-metric-label">Cancelled</div>
            <div class="admin-metric-value text-danger">{{ number_format($stats['cancelled']) }}</div>
        </div>
        <div class="admin-metric-card">
            <div class="admin-metric-label">Expired</div>
            <div class="admin-metric-value text-warning">{{ number_format($stats['expired']) }}</div>
        </div>
        <div class="admin-metric-card">
            <div class="admin-metric-label">On Trial</div>
            <div class="admin-metric-value text-info">{{ number_format($stats['trial']) }}</div>
        </div>
    </div>

    <div class="admin-grid">
        <div class="admin-panel">
            <div class="admin-panel-head">
                <div class="admin-panel-title">Growth vs Churn (Last 6 Months)</div>
            </div>
            <div class="admin-panel-body">
                <canvas id="churnTrendChart" height="300"></canvas>
            </div>
        </div>

        <div class="admin-panel">
            <div class="admin-panel-head">
                <div class="admin-panel-title">Subscription Status Split</div>
            </div>
            <div class="admin-panel-body" style="max-height: 300px;">
                <canvas id="statusSplitChart"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const churnTrendCtx = document.getElementById('churnTrendChart').getContext('2d');
    new Chart(churnTrendCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($newSubTrend->pluck('month')) !!},
            datasets: [
                {
                    label: 'New Subscriptions',
                    data: {!! json_encode($newSubTrend->pluck('count')) !!},
                    borderColor: '#10b981',
                    backgroundColor: 'transparent',
                    tension: 0.4
                },
                {
                    label: 'Cancellations',
                    data: {!! json_encode($churnTrend->pluck('count')) !!},
                    borderColor: '#ef4444',
                    backgroundColor: 'transparent',
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    });

    const statusSplitCtx = document.getElementById('statusSplitChart').getContext('2d');
    new Chart(statusSplitCtx, {
        type: 'doughnut',
        data: {
            labels: ['Active', 'Cancelled', 'Expired', 'Trial'],
            datasets: [{
                data: [{{ $stats['active'] }}, {{ $stats['cancelled'] }}, {{ $stats['expired'] }}, {{ $stats['trial'] }}],
                backgroundColor: ['#10b981', '#ef4444', '#f59e0b', '#3b82f6']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom' } }
        }
    });
</script>
@endsection
