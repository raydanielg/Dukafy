@extends('admin.layouts.app')

@section('page_title', 'Tax Settings')

@section('content')
<div class="admin-page animate__animated animate__fadeIn">
    <div class="admin-page-head">
        <div class="admin-page-title">Tax Settings</div>
    </div>

    <div class="admin-panel">
        <div class="admin-panel-body">
            <form method="POST" action="{{ route('admin.finance.tax_settings.update') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">VAT Percent</label>
                        <input type="number" min="0" max="100" name="vat_percent" value="{{ old('vat_percent', $settings->vat_percent) }}" class="form-control" required>
                    </div>
                    <div class="col-md-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="vat_enabled" name="vat_enabled" {{ $settings->vat_enabled ? 'checked' : '' }}>
                            <label class="form-check-label" for="vat_enabled">Enable VAT</label>
                        </div>
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
