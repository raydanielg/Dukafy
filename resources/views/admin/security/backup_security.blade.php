@extends('admin.layouts.app')

@section('page_title', 'Backup Security')

@section('content')
<div class="admin-page animate__animated animate__fadeIn">
    <div class="admin-page-head">
        <div class="admin-page-title">Backup Security</div>
    </div>

    <div class="admin-panel">
        <div class="admin-panel-body">
            <form method="POST" action="{{ route('admin.security.backup_security.update') }}">
                @csrf

                <div class="row g-3">
                    <div class="col-md-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" id="backup_encryption_enabled" name="backup_encryption_enabled" {{ $settings->backup_encryption_enabled ? 'checked' : '' }}>
                            <label class="form-check-label" for="backup_encryption_enabled">Encrypt backups with password</label>
                        </div>
                        <div class="form-text">This is a stored setting. Integrate with your backup process later.</div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Backup Password</label>
                        <input type="text" name="backup_password" value="{{ old('backup_password', $settings->backup_password) }}" class="form-control" placeholder="Optional">
                    </div>

                    <div class="col-md-12">
                        <button class="admin-action-btn" type="submit">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
