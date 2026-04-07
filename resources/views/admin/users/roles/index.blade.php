@extends('admin.layouts.app')

@section('page_title', 'User Roles')

@section('content')
<div class="admin-page animate__animated animate__fadeIn">
    <div class="admin-page-head">
        <div class="admin-page-title">User Roles</div>
        <div class="admin-page-actions">
            <a href="{{ route('admin.roles.create') }}" class="admin-action-btn">Add Role</a>
        </div>
    </div>

    <div class="admin-panel">
        <div class="admin-panel-body">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Slug</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($roles as $role)
                            <tr class="animate__animated animate__fadeInUp">
                                <td class="fw-semibold">{{ $role->name }}</td>
                                <td class="text-muted">{{ $role->slug }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center text-muted py-4">No roles yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
