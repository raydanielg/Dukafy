@extends('admin.layouts.app')

@section('page_title', 'Add Group')

@section('content')
<div class="admin-page animate__animated animate__fadeIn">
    <div class="admin-page-head">
        <div class="admin-page-title">Add Group</div>
        <div class="admin-page-actions">
            <a href="{{ route('admin.groups.index') }}" class="admin-action-btn admin-action-btn-ghost">Back</a>
        </div>
    </div>

    <div class="admin-panel">
        <div class="admin-panel-body">
            <form method="POST" action="{{ route('admin.groups.store') }}" class="admin-form">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Group Name</label>
                        <input type="text" name="name" value="{{ old('name') }}" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Slug</label>
                        <input type="text" name="slug" value="{{ old('slug') }}" class="form-control" required placeholder="e.g. sales-team">
                    </div>
                    <div class="col-md-12">
                        <button class="admin-action-btn" type="submit">Save Group</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
