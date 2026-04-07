@extends('admin.layouts.app')

@section('page_title', 'Products')

@section('content')
<div class="admin-page">
    <div class="admin-page-head">
        <div class="admin-page-title">Products</div>
        <div class="admin-page-actions">
            <a href="{{ route('admin.business_data.products.create') }}" class="admin-action-btn">New Product</a>
        </div>
    </div>

    <div class="admin-panel">
        <div class="admin-panel-body">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>SKU</th>
                            <th>Business</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $p)
                            <tr>
                                <td class="fw-semibold">{{ $p->name }}</td>
                                <td class="text-muted">{{ $p->sku ?? '—' }}</td>
                                <td>{{ $p->business_name ?? '—' }}</td>
                                <td>{{ $p->category_name ?? '—' }}</td>
                                <td>{{ number_format((float) $p->price, 2) }}</td>
                                <td>{{ $p->stock_qty }}</td>
                                <td>{!! $p->is_active ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>' !!}</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.business_data.products.edit', $p->id) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <form method="POST" action="{{ route('admin.business_data.products.destroy', $p->id) }}" class="d-inline" onsubmit="return confirm('Delete this product?');">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">No products yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $products->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
