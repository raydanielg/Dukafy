@extends('admin.layouts.app')

@section('page_title', 'Bulk Import/Export')

@section('content')
<div class="admin-page">
    <div class="admin-page-head">
        <div class="admin-page-title">Bulk Import/Export</div>
    </div>

    <div class="admin-grid">
        <div class="admin-panel">
            <div class="admin-panel-head">
                <div class="admin-panel-title">Export Products</div>
            </div>
            <div class="admin-panel-body">
                <a href="{{ route('admin.business_data.bulk_import_export.products.export') }}" class="admin-action-btn">Download CSV</a>
            </div>
        </div>

        <div class="admin-panel">
            <div class="admin-panel-head">
                <div class="admin-panel-title">Import Products</div>
            </div>
            <div class="admin-panel-body">
                <form method="POST" action="{{ route('admin.business_data.bulk_import_export.products.import') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <input type="file" name="file" class="form-control" required>
                    </div>
                    <button class="admin-action-btn" type="submit">Import CSV</button>
                </form>
                <div class="text-muted mt-3" style="font-size: 13px;">
                    CSV headers: business_slug, category_slug, name, sku, price, cost, stock_qty, low_stock_threshold, is_active
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
