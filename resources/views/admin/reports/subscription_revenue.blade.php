@extends('admin.layouts.app')

@section('page_title', 'Subscription Revenue Report')

@section('content')
<div class="admin-page">
    <div class="admin-page-head">
        <div class="admin-page-title">Subscription Revenue</div>
    </div>

    <div class="admin-metrics mb-4">
        <div class="admin-metric-card">
            <div class="admin-metric-label">Total Revenue</div>
            <div class="admin-metric-value text-success">{{ number_format($stats['total_revenue'], 2) }}</div>
        </div>
        <div class="admin-metric-card">
            <div class="admin-metric-label">Unpaid Invoices</div>
            <div class="admin-metric-value text-danger">{{ number_format($stats['unpaid_invoices'], 2) }}</div>
        </div>
        <div class="admin-metric-card">
            <div class="admin-metric-label">Active Subscriptions</div>
            <div class="admin-metric-value text-primary">{{ number_format($stats['active_subscriptions']) }}</div>
        </div>
    </div>

    <div class="admin-grid">
        <div class="admin-panel">
            <div class="admin-panel-head">
                <div class="admin-panel-title">Revenue Trend (Last 6 Months)</div>
            </div>
            <div class="admin-panel-body">
                <canvas id="revenueTrendChart" height="250"></canvas>
            </div>
        </div>

        <div class="admin-panel">
            <div class="admin-panel-head">
                <div class="admin-panel-title">Plan Distribution (Active)</div>
            </div>
            <div class="admin-panel-body" style="max-height: 250px;">
                <canvas id="planDistChart"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const revenueTrendCtx = document.getElementById('revenueTrendChart').getContext('2d');
    new Chart(revenueTrendCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($revenueTrend->pluck('month')) !!},
            datasets: [{
                label: 'Revenue',
                data: {!! json_encode($revenueTrend->pluck('total')) !!},
                backgroundColor: '#10b981',
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: { y: { beginAtZero: true } }
        }
    });

    const planDistCtx = document.getElementById('planDistChart').getContext('2d');
    new Chart(planDistCtx, {
        type: 'pie',
        data: {
            labels: {!! json_encode($planDist->pluck('name')) !!},
            datasets: [{
                data: {!! json_encode($planDist->pluck('count')) !!},
                backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'right' } }
        }
    });
</script>
@endsection
