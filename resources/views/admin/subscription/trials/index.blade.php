@extends('admin.layouts.app')

@section('page_title', 'Trial Requests')

@section('content')
<div class="admin-page animate__animated animate__fadeIn">
    <div class="admin-page-head">
        <div class="admin-page-title">Trial Requests</div>
    </div>

    <div class="admin-panel">
        <div class="admin-panel-body">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Requested Days</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $req)
                            <tr class="animate__animated animate__fadeInUp">
                                <td>
                                    <div class="fw-semibold">{{ $req->user_name }}</div>
                                    <div class="text-muted small">{{ $req->user_email }}</div>
                                </td>
                                <td class="text-muted">{{ $req->requested_days }}</td>
                                <td><span class="badge bg-secondary">{{ ucfirst($req->status) }}</span></td>
                                <td class="text-end">
                                    @if($req->status === 'pending')
                                        <form method="POST" action="{{ route('admin.subscription.trials.approve', $req->id) }}" class="d-inline">
                                            @csrf
                                            <button class="btn btn-sm btn-outline-success" type="submit">Approve</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.subscription.trials.reject', $req->id) }}" class="d-inline">
                                            @csrf
                                            <button class="btn btn-sm btn-outline-danger" type="submit">Reject</button>
                                        </form>
                                    @else
                                        <span class="text-muted">{{ \Carbon\Carbon::parse($req->created_at)->format('M d, Y') }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">No trial requests.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">{{ $requests->links() }}</div>
        </div>
    </div>
</div>
@endsection
