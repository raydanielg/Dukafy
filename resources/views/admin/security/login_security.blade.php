@extends('admin.layouts.app')

@section('page_title', 'Login Security')

@section('content')
<div class="admin-page animate__animated animate__fadeIn">
    <div class="admin-page-head">
        <div class="admin-page-title">Login Security</div>
    </div>

    <div class="admin-panel">
        <div class="admin-panel-body">
            <form method="POST" action="{{ route('admin.security.login_security.update') }}">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Session Timeout (minutes)</label>
                        <input type="number" min="5" name="session_timeout_minutes" value="{{ old('session_timeout_minutes', $settings->session_timeout_minutes) }}" class="form-control">
                        <div class="form-text">Auto logout after inactivity.</div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Max Failed Attempts</label>
                        <input type="number" min="1" name="max_failed_attempts" value="{{ old('max_failed_attempts', $settings->max_failed_attempts) }}" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Lockout Duration (minutes)</label>
                        <input type="number" min="1" name="lockout_minutes" value="{{ old('lockout_minutes', $settings->lockout_minutes) }}" class="form-control" required>
                    </div>

                    <div class="col-md-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" id="two_factor_enabled" name="two_factor_enabled" {{ $settings->two_factor_enabled ? 'checked' : '' }}>
                            <label class="form-check-label" for="two_factor_enabled">Enable Two-Factor Authentication (2FA)</label>
                        </div>
                        <div class="form-text">Placeholder switch: implementation can be added later.</div>
                    </div>

                    <div class="col-md-12">
                        <button class="admin-action-btn" type="submit">Save Settings</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
