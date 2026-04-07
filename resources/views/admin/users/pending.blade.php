@extends('admin.layouts.app')

@section('page_title', 'Pending Approvals')

@section('content')
<div class="admin-page animate__animated animate__fadeIn">
    <div class="admin-page-head">
        <div class="admin-page-title">Pending Approvals</div>
        <div class="admin-page-actions">
            <a href="{{ route('admin.users.index') }}" class="admin-action-btn admin-action-btn-ghost">All Users</a>
        </div>
    </div>

    <div class="admin-panel">
        <div class="admin-panel-body">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr class="animate__animated animate__fadeInUp">
                                <td class="fw-semibold">{{ $user->name }}</td>
                                <td class="text-muted">{{ $user->email }}</td>
                                <td class="text-end">
                                    <form method="POST" action="{{ route('admin.users.approve', $user->id) }}" class="d-inline">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-success" type="submit">Approve</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.users.ban', $user->id) }}" class="d-inline">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-danger" type="submit">Ban</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">No pending users.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
