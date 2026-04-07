@extends('admin.layouts.app')

@section('page_title', 'Session Management')

@section('content')
<div class="admin-page animate__animated animate__fadeIn">
    <div class="admin-page-head">
        <div class="admin-page-title">Session Management</div>
    </div>

    <div class="admin-panel">
        <div class="admin-panel-body">
            <div class="alert alert-info">This shows active sessions from the `sessions` table (database driver).</div>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>IP</th>
                            <th>User Agent</th>
                            <th class="text-end">Last Activity</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sessions as $row)
                            <tr class="animate__animated animate__fadeInUp">
                                <td>
                                    <div class="fw-semibold">{{ $row->user_name ?? 'Guest' }}</div>
                                    <div class="text-muted small">{{ $row->user_email ?? '' }}</div>
                                </td>
                                <td class="text-muted">{{ $row->ip_address ?? '—' }}</td>
                                <td class="text-muted" style="max-width: 380px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $row->user_agent ?? '—' }}</td>
                                <td class="text-end text-muted">{{ \Carbon\Carbon::createFromTimestamp($row->last_activity)->format('M d, Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">No sessions found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">{{ $sessions->links() }}</div>
        </div>
    </div>
</div>
@endsection
