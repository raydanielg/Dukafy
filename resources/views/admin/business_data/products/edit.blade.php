@extends('admin.layouts.app')

@section('page_title', 'Edit Product')

@section('content')
<div class="admin-page">
    <div class="admin-page-head">
        <div class="admin-page-title">Edit Product</div>
        <div class="admin-page-actions">
            <a href="{{ route('admin.business_data.products.index') }}" class="admin-action-btn admin-action-btn-ghost">Back</a>
        </div>
    </div>

    <div class="admin-panel">
        <div class="admin-panel-body">
            <form method="POST" action="{{ route('admin.business_data.products.update', $product->id) }}">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Business</label>
                        <select name="business_id" class="form-select" required>
                            @foreach($businesses as $b)
                                <option value="{{ $b->id }}" @selected(old('business_id', $product->business_id) == $b->id)>{{ $b->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Category</label>
                        <select name="category_id" class="form-select">
                            <option value="">None</option>
                            @foreach($categories as $c)
                                <option value="{{ $c->id }}" @selected(old('category_id', $product->category_id) == $c->id)>{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Name</label>
                        <input name="name" class="form-control" value="{{ old('name', $product->name) }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">SKU</label>
                        <input name="sku" class="form-control" value="{{ old('sku', $product->sku) }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Price</label>
                        <input type="number" step="0.01" min="0" name="price" class="form-control" value="{{ old('price', $product->price) }}" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Cost</label>
                        <input type="number" step="0.01" min="0" name="cost" class="form-control" value="{{ old('cost', $product->cost) }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Stock Qty</label>
                        <input type="number" name="stock_qty" class="form-control" value="{{ old('stock_qty', $product->stock_qty) }}" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Low Stock Threshold</label>
                        <input type="number" name="low_stock_threshold" class="form-control" value="{{ old('low_stock_threshold', $product->low_stock_threshold) }}" required>
                    </div>

                    <div class="col-md-8 d-flex align-items-end">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" @checked(old('is_active', $product->is_active))>
                            <label class="form-check-label" for="is_active">Active</label>
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
