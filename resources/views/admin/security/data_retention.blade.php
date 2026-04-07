@extends('admin.layouts.app')

@section('page_title', 'Data Retention Policy')

@section('content')
<div class="admin-page animate__animated animate__fadeIn">
    <div class="admin-page-head">
        <div class="admin-page-title">Data Retention Policy</div>
    </div>

    <div class="admin-panel">
        <div class="admin-panel-body">
            <form method="POST" action="{{ route('admin.security.data_retention.update') }}">
                @csrf

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Sales (days)</label>
                        <input type="number" min="1" name="sales_days" value="{{ old('sales_days', $policy->sales_days) }}" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Security Logs (days)</label>
                        <input type="number" min="1" name="logs_days" value="{{ old('logs_days', $policy->logs_days) }}" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Login History (days)</label>
                        <input type="number" min="1" name="login_history_days" value="{{ old('login_history_days', $policy->login_history_days) }}" class="form-control">
                    </div>

                    <div class="col-md-12">
                        <button class="admin-action-btn" type="submit">Save Policy</button>
                    </div>
                </div>
            </form>

            <div class="alert alert-warning mt-3">
                Note: automated deletion jobs can be implemented later using Laravel Scheduler.
            </div>
        </div>
    </div>
</div>
@endsection
