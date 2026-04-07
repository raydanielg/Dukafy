@extends('admin.layouts.app')

@section('page_title', 'New Product')

@section('content')
<div class="admin-page">
    <div class="admin-page-head">
        <div class="admin-page-title">New Product</div>
        <div class="admin-page-actions">
            <a href="{{ route('admin.business_data.products.index') }}" class="admin-action-btn admin-action-btn-ghost">Back</a>
        </div>
    </div>

    <div class="admin-panel">
        <div class="admin-panel-body">
            <form method="POST" action="{{ route('admin.business_data.products.store') }}">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Business</label>
                        <select name="business_id" class="form-select" required>
                            <option value="">Select business</option>
                            @foreach($businesses as $b)
                                <option value="{{ $b->id }}" @selected(old('business_id') == $b->id)>{{ $b->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Category</label>
                        <select name="category_id" class="form-select">
                            <option value="">None</option>
                            @foreach($categories as $c)
                                <option value="{{ $c->id }}" @selected(old('category_id') == $c->id)>{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Name</label>
                        <input name="name" class="form-control" value="{{ old('name') }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">SKU</label>
                        <input name="sku" class="form-control" value="{{ old('sku') }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Price</label>
                        <input type="number" step="0.01" min="0" name="price" class="form-control" value="{{ old('price', 0) }}" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Cost</label>
                        <input type="number" step="0.01" min="0" name="cost" class="form-control" value="{{ old('cost') }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Stock Qty</label>
                        <input type="number" name="stock_qty" class="form-control" value="{{ old('stock_qty', 0) }}" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Low Stock Threshold</label>
                        <input type="number" name="low_stock_threshold" class="form-control" value="{{ old('low_stock_threshold', 0) }}" required>
                    </div>

                    <div class="col-md-8 d-flex align-items-end">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" @checked(old('is_active', true))>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                    </div>

                    <div class="col-12">
                        <button class="admin-action-btn" type="submit">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
