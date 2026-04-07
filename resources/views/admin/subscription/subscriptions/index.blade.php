@extends('admin.layouts.app')

@section('page_title', 'All Subscriptions')

@section('content')
<div class="admin-page animate__animated animate__fadeIn">
    <div class="admin-page-head">
        <div class="admin-page-title">All Subscriptions</div>
        <div class="admin-page-actions">
            <a href="{{ route('admin.subscription.assign') }}" class="admin-action-btn">Assign Plan to User</a>
        </div>
    </div>

    <div class="admin-panel">
        <div class="admin-panel-body">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Plan</th>
                            <th>Status</th>
                            <th>Start</th>
                            <th>End</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($subscriptions as $sub)
                            <tr class="animate__animated animate__fadeInUp">
                                <td>
                                    <div class="fw-semibold">{{ $sub->user_name }}</div>
                                    <div class="text-muted small">{{ $sub->user_email }}</div>
                                </td>
                                <td>{{ $sub->plan_name ?? '—' }}</td>
                                <td><span class="badge bg-secondary">{{ ucfirst($sub->status) }}</span></td>
                                <td class="text-muted">{{ $sub->starts_at ?? '—' }}</td>
                                <td class="text-muted">{{ $sub->ends_at ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">No subscriptions yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">{{ $subscriptions->links() }}</div>
        </div>
    </div>
</div>
@endsection
