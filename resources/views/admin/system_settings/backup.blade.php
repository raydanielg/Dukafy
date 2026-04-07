@extends('admin.layouts.app')

@section('page_title', 'Backup Settings')

@section('content')
<div class="admin-page">
    <div class="admin-page-head">
        <div class="admin-page-title">Backup Settings</div>
    </div>

    <div class="admin-panel">
        <div class="admin-panel-body">
            <form method="POST" action="{{ route('admin.system_settings.backup.update') }}">
                @csrf

                <div class="row g-3">
                    <div class="col-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="backup_enabled" id="backup_enabled" @checked(old('backup_enabled', ($settings['backup_enabled'] ?? '0') === '1'))>
                            <label class="form-check-label" for="backup_enabled">Enable Backups</label>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Schedule</label>
                        <input name="backup_schedule" class="form-control" value="{{ old('backup_schedule', $settings['backup_schedule'] ?? '') }}" placeholder="daily, weekly, cron expression">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Disk</label>
                        <input name="backup_disk" class="form-control" value="{{ old('backup_disk', $settings['backup_disk'] ?? '') }}" placeholder="local, s3">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Retention Days</label>
                        <input type="number" min="1" name="backup_retention_days" class="form-control" value="{{ old('backup_retention_days', $settings['backup_retention_days'] ?? '') }}">
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
