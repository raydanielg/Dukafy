@extends('admin.layouts.app')

@section('page_title', 'Product Categories')

@section('content')
<div class="admin-page">
    <div class="admin-page-head">
        <div class="admin-page-title">Product Categories</div>
        <div class="admin-page-actions">
            <a href="{{ route('admin.business_data.product_categories.create') }}" class="admin-action-btn">New Category</a>
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
                            <th>Business</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $c)
                            <tr>
                                <td class="fw-semibold">{{ $c->name }}</td>
                                <td class="text-muted">{{ $c->slug }}</td>
                                <td>{{ $c->business_name ?? '—' }}</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.business_data.product_categories.edit', $c->id) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <form method="POST" action="{{ route('admin.business_data.product_categories.destroy', $c->id) }}" class="d-inline" onsubmit="return confirm('Delete this category?');">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">No categories yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $categories->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
