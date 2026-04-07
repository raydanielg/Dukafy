@extends('admin.layouts.app')

@section('page_title', 'Cancelled Subscriptions')

@section('content')
<div class="admin-page animate__animated animate__fadeIn">
    <div class="admin-page-head">
        <div class="admin-page-title">Cancelled Subscriptions</div>
    </div>

    <div class="admin-panel">
        <div class="admin-panel-body">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Plan</th>
                            <th>Cancelled At</th>
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
                                <td class="text-muted">{{ $sub->cancelled_at ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">No cancelled subscriptions.</td>
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
