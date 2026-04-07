@extends('admin.layouts.app')

@section('page_title', 'Business Defaults')

@section('content')
<div class="admin-page">
    <div class="admin-page-head">
        <div class="admin-page-title">Business Defaults</div>
    </div>

    <div class="admin-panel">
        <div class="admin-panel-body">
            <form method="POST" action="{{ route('admin.system_settings.business_defaults.update') }}">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Default Low Stock Threshold</label>
                        <input type="number" min="0" name="default_low_stock_threshold" class="form-control" value="{{ old('default_low_stock_threshold', $settings['default_low_stock_threshold'] ?? '') }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Default Payment Method</label>
                        <input name="default_payment_method" class="form-control" value="{{ old('default_payment_method', $settings['default_payment_method'] ?? '') }}">
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
