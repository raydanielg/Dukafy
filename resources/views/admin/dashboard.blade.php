@extends('admin.layouts.app')

@section('page_title', 'Super Admin Dashboard')

@section('content')
<div class="admin-dashboard">
    <!-- Row 1: Key Metrics -->
    <div class="kpi-grid">
        <!-- KPI 1: Total Users -->
        <div class="kpi-card">
            <div class="kpi-main">
                <div class="kpi-content">
                    <div class="kpi-label">Total Users</div>
                    <div class="kpi-value">{{ number_format($stats['total_users']) }}</div>
                    <div class="kpi-trend">
                        <span class="kpi-trend-up">+{{ $stats['new_users_today'] }} today</span>
                    </div>
                </div>
                <div class="kpi-icon kpi-icon-users">
                    <i class="bi bi-people-fill"></i>
                </div>
            </div>
        </div>

        <!-- KPI 2: Businesses -->
        <div class="kpi-card">
            <div class="kpi-main">
                <div class="kpi-content">
                    <div class="kpi-label">Businesses</div>
                    <div class="kpi-value">{{ number_format($stats['total_businesses']) }}</div>
                    <div class="kpi-trend">
                        <span class="kpi-trend-info">{{ $stats['active_subscriptions'] }} active plans</span>
                    </div>
                </div>
                <div class="kpi-icon kpi-icon-business">
                    <i class="bi bi-building-fill"></i>
                </div>
            </div>
        </div>

        <!-- KPI 3: Sales MTD -->
        <div class="kpi-card">
            <div class="kpi-main">
                <div class="kpi-content">
                    <div class="kpi-label">Sales (MTD)</div>
                    <div class="kpi-value">TZS {{ number_format($stats['mtd_sales']) }}</div>
                    <div class="kpi-trend">
                        <span class="kpi-trend-muted">Platform transaction volume</span>
                    </div>
                </div>
                <div class="kpi-icon kpi-icon-sales">
                    <i class="bi bi-cart-fill"></i>
                </div>
            </div>
        </div>

        <!-- KPI 4: Revenue -->
        <div class="kpi-card">
            <div class="kpi-main">
                <div class="kpi-content">
                    <div class="kpi-label">Subscription Revenue</div>
                    <div class="kpi-value">TZS {{ number_format($stats['total_revenue']) }}</div>
                    <div class="kpi-trend">
                        <span class="kpi-trend-muted">Total paid platform fees</span>
                    </div>
                </div>
                <div class="kpi-icon kpi-icon-revenue">
                    <i class="bi bi-wallet2"></i>
                </div>
            </div>
        </div>

        <!-- KPI 5: Support -->
        <div class="kpi-card">
            <div class="kpi-main">
                <div class="kpi-content">
                    <div class="kpi-label">Support & Approvals</div>
                    <div class="kpi-value">{{ $stats['open_tickets'] + $stats['pending_approvals'] }}</div>
                    <div class="kpi-trend">
                        <span class="kpi-trend-danger">{{ $stats['open_tickets'] }} open tickets</span> • 
                        <span class="kpi-trend-warning">{{ $stats['pending_approvals'] }} pending</span>
                    </div>
                </div>
                <div class="kpi-icon kpi-icon-support">
                    <i class="bi bi-headset"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 2: Charts & Trends -->
    <div class="row g-3 mt-1">
        <div class="col-lg-8">
            <div class="admin-panel h-100">
                <div class="admin-panel-head">
                    <div class="admin-panel-title">Activity Trend (Last 14 Days)</div>
                    <div class="badge bg-light text-dark border">Real-time</div>
                </div>
                <div class="admin-panel-body">
                    <div style="height: 300px;">
                        <canvas id="activityTrendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="admin-panel h-100">
                <div class="admin-panel-head">
                    <div class="admin-panel-title">Business Distribution</div>
                </div>
                <div class="admin-panel-body d-flex align-items-center justify-content-center">
                    <div style="height: 300px; width: 100%;">
                        <canvas id="distributionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 3: Tables & Actions -->
    <div class="row g-3 mt-1">
        <div class="col-lg-6">
            <div class="admin-panel">
                <div class="admin-panel-head">
                    <div class="admin-panel-title">Recent Businesses</div>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-link text-decoration-none p-0">View All</a>
                </div>
                <div class="admin-panel-body p-0">
                    <div class="table-responsive">
                        <table class="table admin-table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Business</th>
                                    <th>Type</th>
                                    <th class="text-end">Joined</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($stats['recent_businesses'] as $biz)
                                    <tr>
                                        <td class="ps-3 py-3 fw-bold">{{ $biz->name }}</td>
                                        <td><span class="badge bg-light text-dark border">{{ $biz->currency }}</span></td>
                                        <td class="pe-3 py-3 text-end text-muted small">{{ \Carbon\Carbon::parse($biz->created_at)->diffForHumans() }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-center py-4 text-muted">No businesses yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="admin-panel">
                <div class="admin-panel-head">
                    <div class="admin-panel-title">Recent Subscriptions</div>
                    <a href="{{ route('admin.subscription.billing') }}" class="btn btn-sm btn-link text-decoration-none p-0">View All</a>
                </div>
                <div class="admin-panel-body p-0">
                    <div class="table-responsive">
                        <table class="table admin-table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Subscriber</th>
                                    <th>Amount</th>
                                    <th class="text-end">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($stats['recent_payments'] as $pay)
                                    <tr>
                                        <td class="ps-3 py-3 fw-bold">{{ $pay->user_name }}</td>
                                        <td class="fw-bold text-success">TZS {{ number_format($pay->amount) }}</td>
                                        <td class="pe-3 py-3 text-end text-muted small">{{ \Carbon\Carbon::parse($pay->paid_at)->format('M d, Y') }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-center py-4 text-muted">No payments yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row g-3 mt-1 mb-4">
        <div class="col-12">
            <div class="admin-panel">
                <div class="admin-panel-head">
                    <div class="admin-panel-title">System Utilities</div>
                </div>
                <div class="admin-panel-body">
                    <div class="admin-actions d-flex gap-3">
                        <a href="{{ route('admin.subscription.plans.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-2">
                            <i class="bi bi-plus-circle"></i> Create New Plan
                        </a>
                        <a href="{{ route('admin.articles.create') }}" class="btn btn-outline-dark d-inline-flex align-items-center gap-2">
                            <i class="bi bi-megaphone"></i> Post Announcement
                        </a>
                        <form method="POST" action="{{ route('admin.system_settings.clear_cache') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger d-inline-flex align-items-center gap-2">
                                <i class="bi bi-trash"></i> Clear Cache
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .admin-dashboard {
        padding: 20px;
        max-width: 1600px;
        margin: 0 auto;
    }

    /* KPI Grid Layout */
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 20px;
        margin-bottom: 24px;
    }

    @media (max-width: 1400px) {
        .kpi-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media (max-width: 992px) {
        .kpi-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 576px) {
        .kpi-grid {
            grid-template-columns: 1fr;
        }
    }

    /* KPI Card Styling */
    .kpi-card {
        background: #fff;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        transition: all 0.2s ease;
        border: 1px solid #f0f0f0;
    }

    .kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        border-color: #e0e0e0;
    }

    .kpi-main {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
    }

    .kpi-content {
        flex: 1;
        min-width: 0;
    }

    .kpi-label {
        font-size: 12px;
        font-weight: 600;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 8px;
    }

    .kpi-value {
        font-size: 28px;
        font-weight: 700;
        color: #111827;
        line-height: 1.2;
        margin-bottom: 8px;
    }

    .kpi-trend {
        font-size: 13px;
        display: flex;
        flex-wrap: wrap;
        gap: 4px;
        align-items: center;
    }

    .kpi-trend-up {
        color: #059669;
        font-weight: 600;
    }

    .kpi-trend-info {
        color: #2563eb;
        font-weight: 600;
    }

    .kpi-trend-danger {
        color: #dc2626;
        font-weight: 600;
    }

    .kpi-trend-warning {
        color: #d97706;
        font-weight: 600;
    }

    .kpi-trend-muted {
        color: #6b7280;
    }

    /* KPI Icons */
    .kpi-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        flex-shrink: 0;
    }

    .kpi-icon-users {
        background: #fef3c7;
        color: #d97706;
    }

    .kpi-icon-business {
        background: #d1fae5;
        color: #059669;
    }

    .kpi-icon-sales {
        background: #dbeafe;
        color: #2563eb;
    }

    .kpi-icon-revenue {
        background: #ede9fe;
        color: #7c3aed;
    }

    .kpi-icon-support {
        background: #fee2e2;
        color: #dc2626;
    }

    /* Panel Styling */
    .admin-panel {
        background: #fff;
        border: none;
        border-radius: 16px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        overflow: hidden;
        border: 1px solid #f0f0f0;
    }

    .admin-panel-head {
        background: #fff;
        padding: 20px 24px;
        border-bottom: 1px solid #f0f0f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .admin-panel-title {
        font-weight: 700;
        font-size: 16px;
        color: #111827;
    }

    .admin-panel-body {
        padding: 20px 24px;
    }

    /* Table Styling */
    .table {
        margin: 0;
    }

    .table thead th {
        background: #f9fafb;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-weight: 700;
        color: #6b7280;
        border: none;
        padding: 12px 20px;
        white-space: nowrap;
    }

    .table td {
        padding: 14px 20px;
        font-size: 14px;
        color: #374151;
        border-color: #f0f0f0;
        vertical-align: middle;
    }

    .table tbody tr:hover {
        background: #f9fafb;
    }

    /* Buttons */
    .btn {
        font-weight: 600;
        padding: 8px 16px;
        border-radius: 8px;
    }

    .btn-link {
        color: #6b7280;
        text-decoration: none;
    }

    .btn-link:hover {
        color: #111827;
    }
</style>
@endsection

@section('scripts')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Activity Trend Chart
    const trendCtx = document.getElementById('activityTrendChart').getContext('2d');
    new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($chartData['labels']) !!},
            datasets: [
                {
                    label: 'New Users',
                    data: {!! json_encode($chartData['users']) !!},
                    borderColor: '#ff6b00',
                    backgroundColor: 'rgba(255, 107, 0, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointHoverRadius: 6
                },
                {
                    label: 'Payments (K TZS)',
                    data: {!! json_encode(array_map(fn($v) => $v / 1000, $chartData['payments']->toArray())) !!},
                    borderColor: '#198754',
                    backgroundColor: 'transparent',
                    borderWidth: 2,
                    borderDash: [5, 5],
                    fill: false,
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top', labels: { usePointStyle: true, boxWidth: 6, font: { weight: 'bold' } } }
            },
            scales: {
                y: { beginAtZero: true, grid: { display: true, color: 'rgba(0,0,0,0.03)' } },
                x: { grid: { display: false } }
            }
        }
    });

    // Distribution Chart
    const distCtx = document.getElementById('distributionChart').getContext('2d');
    new Chart(distCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($businessDist->pluck('name')) !!},
            datasets: [{
                data: {!! json_encode($businessDist->pluck('count')) !!},
                backgroundColor: ['#ff6b00', '#198754', '#0dcaf0', '#ffc107', '#dc3545', '#6610f2'],
                borderWidth: 0,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8, font: { size: 11, weight: 'bold' } } }
            }
        }
    });
});
</script>
@endsection
