@extends('admin.layouts.app')

@section('page_title', 'Invoice Settings')

@section('content')
<div class="admin-page animate__animated animate__fadeIn">
    <div class="admin-page-head">
        <div class="admin-page-title">Invoice Settings</div>
    </div>

    <div class="admin-panel">
        <div class="admin-panel-body">
            <form method="POST" action="{{ route('admin.finance.invoice_settings.update') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Business Name</label>
                        <input type="text" name="business_name" value="{{ old('business_name', $settings->business_name) }}" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">TIN</label>
                        <input type="text" name="tin" value="{{ old('tin', $settings->tin) }}" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Address</label>
                        <input type="text" name="address" value="{{ old('address', $settings->address) }}" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Currency</label>
                        <input type="text" name="currency" value="{{ old('currency', $settings->currency) }}" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Prefix</label>
                        <input type="text" name="prefix" value="{{ old('prefix', $settings->prefix) }}" class="form-control" placeholder="INV">
                    </div>
                    <div class="col-md-12">
                        <button class="admin-action-btn" type="submit">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
