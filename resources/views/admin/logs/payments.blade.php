@extends('admin.layouts.app')

@section('page_title', 'Payment Logs')

@section('content')
<div class="admin-page">
    <div class="admin-page-head">
        <div class="admin-page-title">Payment Logs</div>
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
                            <th>Provider</th>
                            <th>Event</th>
                            <th>Reference</th>
                            <th>Status</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $l)
                            <tr>
                                <td class="text-muted">#{{ $l->id }}</td>
                                <td>{{ \Carbon\Carbon::parse($l->created_at)->format('M d, Y H:i') }}</td>
                                <td>{{ $l->business_name ?? '—' }}</td>
                                <td>{{ $l->user_name ?? '—' }}</td>
                                <td>{{ $l->provider ?? '—' }}</td>
                                <td>{{ $l->event_type ?? '—' }}</td>
                                <td class="text-muted">{{ $l->reference ?? '—' }}</td>
                                <td>{{ $l->status ?? '—' }}</td>
                                <td class="text-end">{{ $l->amount !== null ? number_format((float) $l->amount, 2) : '—' }} {{ $l->currency ?? '' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">No payment logs yet.</td>
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
