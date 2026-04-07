@extends('admin.layouts.app')

@section('page_title', 'Edit Cron Job')

@section('content')
<div class="admin-page">
    <div class="admin-page-head">
        <div class="admin-page-title">Edit Cron Job</div>
        <div class="admin-page-actions">
            <a href="{{ route('admin.extras.cron_jobs.index') }}" class="admin-action-btn admin-action-btn-ghost">Back</a>
        </div>
    </div>

    <div class="admin-panel">
        <div class="admin-panel-body">
            <form method="POST" action="{{ route('admin.extras.cron_jobs.update', $job->id) }}">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Name</label>
                        <input name="name" class="form-control" value="{{ old('name', $job->name) }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Schedule</label>
                        <input name="schedule" class="form-control" value="{{ old('schedule', $job->schedule) }}">
                    </div>

                    <div class="col-12">
                        <label class="form-label">Command</label>
                        <input name="command" class="form-control" value="{{ old('command', $job->command) }}" required>
                    </div>

                    <div class="col-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="enabled" id="enabled" @checked(old('enabled', $job->enabled))>
                            <label class="form-check-label" for="enabled">Enabled</label>
                        </div>
                    </div>

                    <div class="col-12">
                        <button class="admin-action-btn" type="submit">Save Changes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
