@extends('admin.layouts.app')

@section('page_title', 'Email Settings')

@section('content')
<div class="admin-page">
    <div class="admin-page-head">
        <div class="admin-page-title">Email Settings</div>
    </div>

    <div class="admin-panel">
        <div class="admin-panel-body">
            <form method="POST" action="{{ route('admin.system_settings.email.update') }}">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">From Name</label>
                        <input name="from_name" class="form-control" value="{{ old('from_name', $settings['from_name'] ?? '') }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">From Address</label>
                        <input name="from_address" type="email" class="form-control" value="{{ old('from_address', $settings['from_address'] ?? '') }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">SMTP Host</label>
                        <input name="smtp_host" class="form-control" value="{{ old('smtp_host', $settings['smtp_host'] ?? '') }}">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">SMTP Port</label>
                        <input name="smtp_port" type="number" min="1" class="form-control" value="{{ old('smtp_port', $settings['smtp_port'] ?? '') }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">SMTP Username</label>
                        <input name="smtp_username" class="form-control" value="{{ old('smtp_username', $settings['smtp_username'] ?? '') }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">SMTP Password</label>
                        <input name="smtp_password" type="password" class="form-control" value="{{ old('smtp_password', $settings['smtp_password'] ?? '') }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Encryption</label>
                        <input name="smtp_encryption" class="form-control" value="{{ old('smtp_encryption', $settings['smtp_encryption'] ?? '') }}" placeholder="tls / ssl">
                    </div>

                    <div class="col-12">
                        <button class="admin-action-btn" type="submit">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
