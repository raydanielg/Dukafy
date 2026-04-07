@extends('admin.layouts.app')

@section('page_title', 'Business Performance Report')

@section('content')
<div class="admin-page">
    <div class="admin-page-head">
        <div class="admin-page-title">Business Performance</div>
    </div>

    <div class="admin-grid">
        <div class="admin-panel">
            <div class="admin-panel-head">
                <div class="admin-panel-title">Sales Trend (Last 30 Days)</div>
            </div>
            <div class="admin-panel-body">
                <canvas id="salesTrendChart" height="250"></canvas>
            </div>
        </div>

        <div class="admin-panel">
            <div class="admin-panel-head">
                <div class="admin-panel-title">Category Distribution</div>
            </div>
            <div class="admin-panel-body" style="max-height: 250px;">
                <canvas id="categoryDistChart"></canvas>
            </div>
        </div>

        <div class="admin-panel col-md-12">
            <div class="admin-panel-head">
                <div class="admin-panel-title">Top 10 Businesses by Revenue</div>
            </div>
            <div class="admin-panel-body">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Business Name</th>
                                <th>Sales Count</th>
                                <th class="text-end">Total Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topBusinesses as $b)
                                <tr>
                                    <td class="fw-semibold">{{ $b->name }}</td>
                                    <td>{{ number_format($b->sales_count) }}</td>
                                    <td class="text-end fw-bold">{{ number_format($b->revenue, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">No sales data available.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const salesTrendCtx = document.getElementById('salesTrendChart').getContext('2d');
    new Chart(salesTrendCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($salesTrend->pluck('date')) !!},
            datasets: [{
                label: 'Revenue',
                data: {!! json_encode($salesTrend->pluck('total')) !!},
                borderColor: '#8b5cf6',
                backgroundColor: 'rgba(139, 92, 246, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: { y: { beginAtZero: true } }
        }
    });

    const categoryDistCtx = document.getElementById('categoryDistChart').getContext('2d');
    new Chart(categoryDistCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($categoryDist->pluck('name')) !!},
            datasets: [{
                data: {!! json_encode($categoryDist->pluck('count')) !!},
                backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#6366f1']
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
