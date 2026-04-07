@extends('admin.layouts.app')

@section('page_title', 'Security Alerts')

@section('content')
<div class="admin-page animate__animated animate__fadeIn">
    <div class="admin-page-head">
        <div class="admin-page-title">Security Alerts</div>
    </div>

    <div class="admin-panel">
        <div class="admin-panel-body">
            <form method="POST" action="{{ route('admin.security.security_alerts.update') }}">
                @csrf

                <div class="row g-3">
                    <div class="col-md-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="email_on_new_location_login" name="email_on_new_location_login" {{ $alerts->email_on_new_location_login ? 'checked' : '' }}>
                            <label class="form-check-label" for="email_on_new_location_login">Send email when login is from a new location/device</label>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="whatsapp_on_password_change" name="whatsapp_on_password_change" {{ $alerts->whatsapp_on_password_change ? 'checked' : '' }}>
                            <label class="form-check-label" for="whatsapp_on_password_change">Send WhatsApp alert when password is changed</label>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="alert_on_backup_failure" name="alert_on_backup_failure" {{ $alerts->alert_on_backup_failure ? 'checked' : '' }}>
                            <label class="form-check-label" for="alert_on_backup_failure">Alert when scheduled backup fails</label>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <button class="admin-action-btn" type="submit">Save Alerts</button>
                    </div>
                </div>
            </form>

            <div class="alert alert-info mt-3">Notification delivery (Email/WhatsApp) can be integrated later via your provider.</div>
        </div>
    </div>
</div>
@endsection
