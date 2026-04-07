@extends('admin.layouts.app')

@section('page_title', 'Role Permissions')

@section('content')
<div class="admin-page animate__animated animate__fadeIn">
    <div class="admin-page-head">
        <div class="admin-page-title">Role Permissions</div>
        <div class="admin-page-actions">
            <a href="{{ route('admin.roles.index') }}" class="admin-action-btn admin-action-btn-ghost">Roles</a>
        </div>
    </div>

    <div class="admin-panel">
        <div class="admin-panel-body">
            <div class="row g-3">
                @foreach($roles as $role)
                    @php
                        $checked = $map[$role->id] ?? [];
                    @endphp
                    <div class="col-lg-6">
                        <div class="border rounded p-3 animate__animated animate__fadeInUp">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="fw-bold">{{ $role->name }}</div>
                                <div class="text-muted">{{ $role->slug }}</div>
                            </div>

                            <form method="POST" action="{{ route('admin.permissions.update') }}">
                                @csrf
                                <input type="hidden" name="role_id" value="{{ $role->id }}">

                                <div class="row g-2">
                                    @foreach($permissions as $perm)
                                        <div class="col-12">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="permission_ids[]" value="{{ $perm->id }}" id="perm_{{ $role->id }}_{{ $perm->id }}" {{ in_array($perm->id, $checked) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="perm_{{ $role->id }}_{{ $perm->id }}">
                                                    {{ $perm->name }}
                                                    <span class="text-muted">({{ $perm->slug }})</span>
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="mt-3">
                                    <button class="admin-action-btn" type="submit">Save Permissions</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
