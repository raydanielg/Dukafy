@extends('admin.layouts.app')

@section('page_title', 'Edit Category')

@section('content')
<div class="admin-page animate__animated animate__fadeIn">
    <div class="admin-page-head">
        <div class="admin-page-title">Edit Category</div>
        <div class="admin-page-actions">
            <a href="{{ route('admin.article_categories.index') }}" class="admin-action-btn admin-action-btn-ghost">Back</a>
        </div>
    </div>

    <div class="admin-panel">
        <div class="admin-panel-body">
            <form method="POST" action="{{ route('admin.article_categories.update', $category->id) }}" class="admin-form">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" value="{{ old('name', $category->name) }}" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Slug</label>
                        <input type="text" name="slug" value="{{ old('slug', $category->slug) }}" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Icon (optional)</label>
                        <input type="text" name="icon" value="{{ old('icon', $category->icon) }}" class="form-control">
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Description</label>
                        <textarea name="description" rows="3" class="form-control">{{ old('description', $category->description) }}</textarea>
                    </div>

                    <div class="col-md-12">
                        <button class="admin-action-btn" type="submit">Save Changes</button>
                    </div>
                </div>
            </form>

            <div class="mt-4">
                <form method="POST" action="{{ route('admin.article_categories.destroy', $category->id) }}" onsubmit="return confirm('Delete this category?');">
                    @csrf
                    <button class="btn btn-sm btn-outline-danger" type="submit">Delete Category</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
