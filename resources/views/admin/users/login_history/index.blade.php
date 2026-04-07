@extends('admin.layouts.app')

@section('page_title', 'Login History')

@section('content')
<div class="admin-page animate__animated animate__fadeIn">
    <div class="admin-page-head">
        <div class="admin-page-title">Login History</div>
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
                            <th>IP</th>
                            <th>User Agent</th>
                            <th class="text-end">Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logins as $row)
                            <tr class="animate__animated animate__fadeInUp">
                                <td>
                                    <div class="fw-semibold">{{ $row->user_name ?? '—' }}</div>
                                    <div class="text-muted small">{{ $row->user_email ?? '' }}</div>
                                </td>
                                <td class="text-muted">{{ $row->ip_address ?? '—' }}</td>
                                <td class="text-muted" style="max-width: 380px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $row->user_agent ?? '—' }}</td>
                                <td class="text-end text-muted">{{ \Carbon\Carbon::parse($row->logged_in_at)->format('M d, Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">No login records yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $logins->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
