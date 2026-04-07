@extends('admin.layouts.app')

@section('page_title', 'Cron Jobs')

@section('content')
<div class="admin-page">
    <div class="admin-page-head">
        <div class="admin-page-title">Cron Jobs</div>
        <div class="admin-page-actions">
            <a href="{{ route('admin.extras.cron_jobs.create') }}" class="admin-action-btn">New Cron Job</a>
        </div>
    </div>

    <div class="admin-panel">
        <div class="admin-panel-body">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Command</th>
                            <th>Schedule</th>
                            <th>Status</th>
                            <th>Last Run</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jobs as $j)
                            <tr>
                                <td class="fw-semibold">{{ $j->name }}</td>
                                <td class="text-muted">{{ $j->command }}</td>
                                <td>{{ $j->schedule ?? '—' }}</td>
                                <td>{!! $j->enabled ? '<span class="badge bg-success">Enabled</span>' : '<span class="badge bg-secondary">Disabled</span>' !!}</td>
                                <td>{{ $j->last_run_at ? \Carbon\Carbon::parse($j->last_run_at)->format('M d, Y H:i') : '—' }}</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.extras.cron_jobs.edit', $j->id) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <form method="POST" action="{{ route('admin.extras.cron_jobs.destroy', $j->id) }}" class="d-inline" onsubmit="return confirm('Delete this cron job?');">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">No cron jobs yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $jobs->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
