@extends('admin.layouts.app')

@section('page_title', 'Profile Management')

@section('content')
<div class="admin-page animate__animated animate__fadeIn">
    <div class="admin-page-head">
        <div class="admin-page-title">Profile Management</div>
        <div class="admin-page-actions">
            <a href="{{ route('admin.dashboard') }}" class="admin-action-btn admin-action-btn-ghost">Dashboard</a>
        </div>
    </div>

    <div class="admin-panel">
        <div class="admin-panel-body">
            <form method="POST" action="{{ route('admin.profile.update') }}" class="admin-form">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">New Password (optional)</label>
                        <input type="password" name="password" class="form-control" placeholder="Leave blank to keep current password">
                    </div>

                    <div class="col-md-12">
                        <button class="admin-action-btn" type="submit">Save Changes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
