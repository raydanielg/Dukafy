@extends('admin.layouts.app')

@section('page_title', 'Email Logs')

@section('content')
<div class="admin-page">
    <div class="admin-page-head">
        <div class="admin-page-title">Email Logs</div>
    </div>

    <div class="admin-panel">
        <div class="admin-panel-body">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Created</th>
                            <th>Business</th>
                            <th>User</th>
                            <th>To</th>
                            <th>Subject</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $l)
                            <tr>
                                <td class="text-muted">#{{ $l->id }}</td>
                                <td>{{ \Carbon\Carbon::parse($l->created_at)->format('M d, Y H:i') }}</td>
                                <td>{{ $l->business_name ?? '—' }}</td>
                                <td>{{ $l->user_name ?? '—' }}</td>
                                <td>{{ $l->to ?? '—' }}</td>
                                <td class="fw-semibold">{{ $l->subject ?? '—' }}</td>
                                <td>{{ $l->status ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">No email logs yet.</td>
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
