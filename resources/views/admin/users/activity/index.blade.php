@extends('admin.layouts.app')

@section('page_title', 'User Activity Log')

@section('content')
<div class="admin-page animate__animated animate__fadeIn">
    <div class="admin-page-head">
        <div class="admin-page-title">User Activity Log</div>
        <div class="admin-page-actions">
            <a href="{{ route('admin.users.index') }}" class="admin-action-btn admin-action-btn-ghost">Users</a>
        </div>
    </div>

    <div class="admin-panel">
        <div class="admin-panel-body">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Action</th>
                            <th>Path</th>
                            <th>IP</th>
                            <th class="text-end">Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr class="animate__animated animate__fadeInUp">
                                <td>
                                    <div class="fw-semibold">{{ $log->user_name ?? '—' }}</div>
                                    <div class="text-muted small">{{ $log->user_email ?? '' }}</div>
                                </td>
                                <td class="text-muted">{{ $log->action ?? '—' }}</td>
                                <td class="text-muted">{{ $log->path }}</td>
                                <td class="text-muted">{{ $log->ip_address ?? '—' }}</td>
                                <td class="text-end text-muted">{{ \Carbon\Carbon::parse($log->created_at)->format('M d, Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">No logs yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
