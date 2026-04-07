@extends('admin.layouts.app')

@section('page_title', 'Database Encryption')

@section('content')
<div class="admin-page animate__animated animate__fadeIn">
    <div class="admin-page-head">
        <div class="admin-page-title">Database Encryption</div>
    </div>

    <div class="admin-panel">
        <div class="admin-panel-body">
            <div class="alert alert-warning">
                This is a configuration flag in the admin panel. Full field-level encryption requires implementation in models/services.
            </div>

            <form method="POST" action="{{ route('admin.security.database_encryption.update') }}">
                @csrf

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" value="1" id="database_encryption_enabled" name="database_encryption_enabled" {{ $settings->database_encryption_enabled ? 'checked' : '' }}>
                    <label class="form-check-label" for="database_encryption_enabled">Enable Database Encryption</label>
                </div>

                <button class="admin-action-btn" type="submit">Save</button>
            </form>
        </div>
    </div>
</div>
@endsection
