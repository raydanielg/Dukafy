@extends('admin.layouts.app')

@section('page_title', 'Profit & Loss')

@section('content')
<div class="admin-dashboard animate__animated animate__fadeIn">
    <div class="admin-metrics">
        <div class="admin-metric-card animate__animated animate__fadeInUp">
            <div class="admin-metric-label">Revenue</div>
            <div class="admin-metric-value">TZS {{ number_format($revenue) }}</div>
        </div>
        <div class="admin-metric-card animate__animated animate__fadeInUp">
            <div class="admin-metric-label">Expenses</div>
            <div class="admin-metric-value">TZS {{ number_format($costs) }}</div>
        </div>
        <div class="admin-metric-card animate__animated animate__fadeInUp">
            <div class="admin-metric-label">Net Profit</div>
            <div class="admin-metric-value">TZS {{ number_format($profit) }}</div>
        </div>
        <div class="admin-metric-card animate__animated animate__fadeInUp">
            <div class="admin-metric-label">Status</div>
            <div class="admin-metric-value">{{ $profit >= 0 ? 'Profit' : 'Loss' }}</div>
        </div>
    </div>
</div>
@endsection
