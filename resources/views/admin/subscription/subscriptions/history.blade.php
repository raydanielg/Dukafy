@extends('admin.layouts.app')

@section('page_title', 'Subscription History')

@section('content')
<div class="admin-page animate__animated animate__fadeIn">
    <div class="admin-page-head">
        <div class="admin-page-title">Subscription History</div>
    </div>

    <div class="admin-panel">
        <div class="admin-panel-body">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Event</th>
                            <th class="text-end">Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($history as $row)
                            <tr class="animate__animated animate__fadeInUp">
                                <td>
                                    <div class="fw-semibold">{{ $row->user_name ?? '—' }}</div>
                                    <div class="text-muted small">{{ $row->user_email ?? '' }}</div>
                                </td>
                                <td class="text-muted">{{ $row->event }}</td>
                                <td class="text-end text-muted">{{ \Carbon\Carbon::parse($row->created_at)->format('M d, Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">No history yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">{{ $history->links() }}</div>
        </div>
    </div>
</div>
@endsection
