@extends('admin.layouts.app')

@section('page_title', 'All Users')

@section('content')
<div class="admin-page animate__animated animate__fadeIn">
    <div class="admin-page-head">
        <div class="admin-page-title">All Users</div>
        <div class="admin-page-actions">
            <a href="{{ route('admin.users.create') }}" class="admin-action-btn">Add New User</a>
        </div>
    </div>

    <div class="admin-panel">
        <div class="admin-panel-body">
            <form class="row g-2 mb-3" method="GET" action="{{ route('admin.users.index') }}">
                <div class="col-md-6">
                    <input type="text" name="q" value="{{ $q }}" class="form-control" placeholder="Search by name or email">
                </div>
                <div class="col-md-2">
                    <button class="admin-action-btn" type="submit">Search</button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr class="animate__animated animate__fadeInUp">
                                <td class="fw-semibold">{{ $user->name }}</td>
                                <td class="text-muted">{{ $user->email }}</td>
                                <td>
                                    @if($user->banned_at)
                                        <span class="badge bg-danger">Banned</span>
                                    @elseif($user->approved_at)
                                        <span class="badge bg-success">Approved</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if(!$user->approved_at && !$user->banned_at)
                                        <form method="POST" action="{{ route('admin.users.approve', $user->id) }}" class="d-inline">
                                            @csrf
                                            <button class="btn btn-sm btn-outline-success" type="submit">Approve</button>
                                        </form>
                                    @endif

                                    @if(!$user->banned_at)
                                        <form method="POST" action="{{ route('admin.users.ban', $user->id) }}" class="d-inline">
                                            @csrf
                                            <button class="btn btn-sm btn-outline-danger" type="submit">Ban</button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('admin.users.unban', $user->id) }}" class="d-inline">
                                            @csrf
                                            <button class="btn btn-sm btn-outline-secondary" type="submit">Unban</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
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
