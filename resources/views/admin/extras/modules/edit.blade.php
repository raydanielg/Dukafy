@extends('admin.layouts.app')

@section('page_title', 'Edit Module')

@section('content')
<div class="admin-page">
    <div class="admin-page-head">
        <div class="admin-page-title">Edit Module</div>
        <div class="admin-page-actions">
            <a href="{{ route('admin.extras.modules.index') }}" class="admin-action-btn admin-action-btn-ghost">Back</a>
        </div>
    </div>

    <div class="admin-panel">
        <div class="admin-panel-body">
            <form method="POST" action="{{ route('admin.extras.modules.update', $module->id) }}">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Name</label>
                        <input name="name" class="form-control" value="{{ old('name', $module->name) }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Slug (optional)</label>
                        <input name="slug" class="form-control" value="{{ old('slug', $module->slug) }}">
                    </div>

                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description', $module->description) }}</textarea>
                    </div>

                    <div class="col-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="enabled" id="enabled" @checked(old('enabled', $module->enabled))>
                            <label class="form-check-label" for="enabled">Enabled</label>
                        </div>
                    </div>

                    <div class="col-12">
                        <button class="admin-action-btn" type="submit">Save Changes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
