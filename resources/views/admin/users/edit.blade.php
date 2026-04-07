@extends('admin.layouts.app')

@section('page_title', 'Edit User')

@section('content')
<div class="admin-page animate__animated animate__fadeIn">
    <div class="admin-page-head">
        <div class="admin-page-title">Edit User</div>
        <div class="admin-page-actions">
            <a href="{{ route('admin.users.index') }}" class="admin-action-btn admin-action-btn-ghost">Back</a>
        </div>
    </div>

    <div class="admin-panel">
        <div class="admin-panel-body">
            <form method="POST" action="{{ route('admin.users.update', $user->id) }}" class="admin-form">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">New Password (optional)</label>
                        <input type="password" name="password" class="form-control" placeholder="Leave blank to keep current password">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Role</label>
                        <select name="role_id" class="form-select">
                            <option value="">—</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ (int) $selectedRoleId === (int) $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Business</label>
                        <select name="business_id" class="form-select">
                            <option value="">—</option>
                            @foreach($businesses as $b)
                                <option value="{{ $b->id }}" {{ (int) $user->business_id === (int) $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Branch</label>
                        <select name="branch_id" class="form-select">
                            <option value="">—</option>
                            @foreach($branches as $br)
                                <option value="{{ $br->id }}" {{ (int) $user->branch_id === (int) $br->id ? 'selected' : '' }}>{{ $br->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Group</label>
                        <select name="group_id" class="form-select">
                            <option value="">—</option>
                            @foreach($groups as $group)
                                <option value="{{ $group->id }}" {{ (int) $selectedGroupId === (int) $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Status</label>
                        <div class="d-flex gap-3 align-items-center">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" id="approved" name="approved" {{ $user->approved_at ? 'checked' : '' }}>
                                <label class="form-check-label" for="approved">Approved</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" id="is_admin" name="is_admin" {{ $user->is_admin ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_admin">System Admin</label>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <button class="admin-action-btn" type="submit">Save Changes</button>
                    </div>
                </div>
            </form>

            <div class="mt-4">
                <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}" onsubmit="return confirm('Are you sure you want to delete this user?');">
                    @csrf
                    <button class="btn btn-sm btn-outline-danger" type="submit">Delete User</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
