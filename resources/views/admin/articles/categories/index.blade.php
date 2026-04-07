@extends('admin.layouts.app')

@section('page_title', 'Categories')

@section('content')
<div class="admin-page animate__animated animate__fadeIn">
    <div class="admin-page-head">
        <div class="admin-page-title">Categories</div>
        <div class="admin-page-actions">
            <a href="{{ route('admin.article_categories.create') }}" class="admin-action-btn">Add Category</a>
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
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $cat)
                            <tr class="animate__animated animate__fadeInUp">
                                <td class="fw-semibold">{{ $cat->name }}</td>
                                <td class="text-muted">{{ $cat->slug }}</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.article_categories.edit', $cat->id) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <form method="POST" action="{{ route('admin.article_categories.destroy', $cat->id) }}" class="d-inline" onsubmit="return confirm('Delete this category?');">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">No categories yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
