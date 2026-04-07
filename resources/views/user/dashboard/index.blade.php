@extends('user.layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Top Metrics Grid -->
    <div class="row g-3 mb-4">
        <div class="col-md-4 col-lg-2">
            <div class="card border-0 shadow-sm text-center p-3 h-100">
                <div class="small text-muted mb-1">Active Borrowers</div>
                <div class="h5 fw-bold mb-0">Tsh {{ number_format($stats['active_borrowers_val'], 2) }}</div>
                <div class="text-xs text-muted mt-1">total clients</div>
            </div>
        </div>
        <div class="col-md-4 col-lg-2">
            <div class="card border-0 shadow-sm text-center p-3 h-100">
                <div class="small text-muted mb-1">Pending Loans</div>
                <div class="h5 fw-bold mb-0">Tsh {{ number_format($stats['pending_loans_val'], 2) }}</div>
                <div class="text-xs text-muted mt-1">Awaiting Approval</div>
            </div>
        </div>
        <div class="col-md-4 col-lg-2">
            <div class="card border-0 shadow-sm text-center p-3 h-100">
                <div class="small text-muted mb-1">Overdue</div>
                <div class="h5 fw-bold mb-0 text-danger">{{ number_format($stats['overdue_val'], 2) }}%</div>
                <div class="text-xs text-muted mt-1">Total overdue amount</div>
            </div>
        </div>
        <div class="col-md-4 col-lg-2">
            <div class="card border-0 shadow-sm text-center p-3 h-100">
                <div class="small text-muted mb-1">Today Disb.</div>
                <div class="h5 fw-bold mb-0">Tsh {{ number_format($stats['today_disbursed'], 2) }}</div>
                <div class="text-xs text-muted mt-1">new loans</div>
            </div>
        </div>
        <div class="col-md-4 col-lg-2">
            <div class="card border-0 shadow-sm text-center p-3 h-100">
                <div class="small text-muted mb-1">Today Coll.</div>
                <div class="h5 fw-bold mb-0 text-success">Tsh {{ number_format($stats['today_collected'], 2) }}</div>
                <div class="text-xs text-muted mt-1">collected</div>
            </div>
        </div>
        <div class="col-md-4 col-lg-2">
            <div class="card border-0 shadow-sm text-center p-3 h-100">
                <div class="small text-muted mb-1">Rep. Rate</div>
                <div class="h5 fw-bold mb-0 text-primary">{{ number_format($stats['repayment_rate'], 2) }}%</div>
                <div class="text-xs text-muted mt-1">this month</div>
            </div>
        </div>
    </div>

    <!-- Quick Action Buttons -->
    <div class="row g-3 mb-4">
        <div class="col-4">
            <button class="btn btn-primary w-100 py-3 rounded-4 shadow-sm border-0 d-flex flex-column align-items-center justify-content-center">
                <i class="fa-solid fa-money-bill-transfer mb-2 fs-4"></i>
                <span class="fw-bold">💸+ Loan</span>
            </button>
        </div>
        <div class="col-4">
            <button class="btn btn-info text-white w-100 py-3 rounded-4 shadow-sm border-0 d-flex flex-column align-items-center justify-content-center">
                <i class="fa-solid fa-user-plus mb-2 fs-4"></i>
                <span class="fw-bold">👤+ Borrower</span>
            </button>
        </div>
        <div class="col-4">
            <button class="btn btn-warning text-white w-100 py-3 rounded-4 shadow-sm border-0 d-flex flex-column align-items-center justify-content-center">
                <i class="fa-solid fa-chart-line mb-2 fs-4"></i>
                <span class="fw-bold">📉+ Expense</span>
            </button>
        </div>
    </div>

    <!-- Feature Menu List -->
    <div class="row g-3">
        @php
            $menus = [
                ['icon' => 'fa-users', 'title' => 'Customers', 'sub' => 'Manage Borrowers, clients & profiles', 'color' => 'text-primary'],
                ['icon' => 'fa-hand-holding-dollar', 'title' => 'Loans & Repayments', 'sub' => 'Applications & active loans', 'color' => 'text-success'],
                ['icon' => 'fa-file-invoice-dollar', 'title' => 'Expenses', 'sub' => 'Operational costs', 'color' => 'text-danger'],
                ['icon' => 'fa-vault', 'title' => 'Treasury & Banking', 'sub' => 'Manage teller, Customers and Debts', 'color' => 'text-info'],
                ['icon' => 'fa-comments', 'title' => 'SMS & Emails', 'sub' => 'Send SMS & Emails', 'color' => 'text-warning'],
                ['icon' => 'fa-chart-pie', 'title' => 'Reports', 'sub' => 'Portfolio & performance', 'color' => 'text-secondary'],
                ['icon' => 'fa-user-group', 'title' => 'Add Team', 'sub' => 'Hire, assign & Manage', 'color' => 'text-primary'],
                ['icon' => 'fa-shop', 'title' => 'Add Business', 'sub' => 'Expand locations', 'color' => 'text-success'],
                ['icon' => 'fa-credit-card', 'title' => 'Subscribe', 'sub' => 'Manage your subscription', 'color' => 'text-purple'],
                ['icon' => 'fa-gear', 'title' => 'Settings', 'sub' => 'Account settings & preferences', 'color' => 'text-dark'],
            ];
        @endphp

        @foreach($menus as $m)
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm p-3 h-100 card-hover">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-light p-3 me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="fa-solid {{ $m['icon'] }} {{ $m['color'] }} fs-4"></i>
                    </div>
                    <div>
                        <div class="fw-bold">{{ $m['title'] }}</div>
                        <div class="text-muted small">{{ $m['sub'] }}</div>
                    </div>
                    <div class="ms-auto">
                        <i class="fa-solid fa-chevron-right text-muted opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<style>
    .text-xs { font-size: 0.75rem; }
    .card-hover:hover { 
        background: #f1f5f9; 
        cursor: pointer; 
        transform: translateY(-2px);
        transition: all 0.2s;
    }
    .text-purple { color: #8b5cf6; }
</style>
@endsection
