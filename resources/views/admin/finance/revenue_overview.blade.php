@extends('admin.layouts.app')

@section('page_title', 'Revenue Overview')

@section('content')
<div class="admin-dashboard animate__animated animate__fadeIn">
    <div class="admin-metrics">
        <div class="admin-metric-card animate__animated animate__fadeInUp">
            <div class="admin-metric-label">Total Payments</div>
            <div class="admin-metric-value">TZS {{ number_format($totalPaid) }}</div>
        </div>
        <div class="admin-metric-card animate__animated animate__fadeInUp">
            <div class="admin-metric-label">Total Invoices</div>
            <div class="admin-metric-value">TZS {{ number_format($totalInvoices) }}</div>
        </div>
        <div class="admin-metric-card animate__animated animate__fadeInUp">
            <div class="admin-metric-label">Unpaid</div>
            <div class="admin-metric-value">TZS {{ number_format($unpaid) }}</div>
        </div>
        <div class="admin-metric-card animate__animated animate__fadeInUp">
            <div class="admin-metric-label">Currency</div>
            <div class="admin-metric-value">TZS</div>
        </div>
    </div>
</div>
@endsection
