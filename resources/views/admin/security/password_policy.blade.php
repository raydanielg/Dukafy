@extends('admin.layouts.app')

@section('page_title', 'Password Policy')

@section('content')
<div class="admin-page animate__animated animate__fadeIn">
    <div class="admin-page-head">
        <div class="admin-page-title">Password Policy</div>
    </div>

    <div class="admin-panel">
        <div class="admin-panel-body">
            <form method="POST" action="{{ route('admin.security.password_policy.update') }}">
                @csrf

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Minimum Length</label>
                        <input type="number" min="6" max="64" name="password_min_length" value="{{ old('password_min_length', $settings->password_min_length) }}" class="form-control" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Password Expiry (days)</label>
                        <input type="number" min="1" name="password_expire_days" value="{{ old('password_expire_days', $settings->password_expire_days) }}" class="form-control">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Password History Count</label>
                        <input type="number" min="1" name="password_history_count" value="{{ old('password_history_count', $settings->password_history_count) }}" class="form-control">
                    </div>

                    <div class="col-md-12">
                        <div class="row g-2">
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="req_up" name="password_require_uppercase" {{ $settings->password_require_uppercase ? 'checked' : '' }}>
                                    <label class="form-check-label" for="req_up">Require uppercase</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="req_low" name="password_require_lowercase" {{ $settings->password_require_lowercase ? 'checked' : '' }}>
                                    <label class="form-check-label" for="req_low">Require lowercase</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="req_num" name="password_require_number" {{ $settings->password_require_number ? 'checked' : '' }}>
                                    <label class="form-check-label" for="req_num">Require number</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="req_sym" name="password_require_symbol" {{ $settings->password_require_symbol ? 'checked' : '' }}>
                                    <label class="form-check-label" for="req_sym">Require symbol</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <button class="admin-action-btn" type="submit">Save Policy</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
