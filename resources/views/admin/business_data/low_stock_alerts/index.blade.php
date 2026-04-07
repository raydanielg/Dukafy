@extends('admin.layouts.app')

@section('page_title', 'Low Stock Alerts')

@section('content')
<div class="admin-page">
    <div class="admin-page-head">
        <div class="admin-page-title">Low Stock Alerts</div>
        <div class="admin-page-actions">
            <a href="{{ route('admin.business_data.products.index') }}" class="admin-action-btn admin-action-btn-ghost">Manage Products</a>
        </div>
    </div>

    <div class="admin-panel">
        <div class="admin-panel-body">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Business</th>
                            <th>Category</th>
                            <th>Stock</th>
                            <th>Threshold</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $p)
                            <tr>
                                <td class="fw-semibold">{{ $p->name }}</td>
                                <td>{{ $p->business_name ?? '—' }}</td>
                                <td>{{ $p->category_name ?? '—' }}</td>
                                <td><span class="badge bg-danger">{{ $p->stock_qty }}</span></td>
                                <td>{{ $p->low_stock_threshold }}</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.business_data.products.edit', $p->id) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">No low stock products.</td>
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
