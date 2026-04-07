@extends('admin.layouts.app')

@section('page_title', 'Payment Gateway')

@section('content')
<div class="admin-page animate__animated animate__fadeIn">
    <div class="admin-page-head">
        <div class="admin-page-title">Payment Gateway</div>
    </div>

    <div class="admin-panel">
        <div class="admin-panel-body">
            <form method="POST" action="{{ route('admin.finance.payment_gateway.update') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Provider</label>
                        <input type="text" name="provider" value="{{ old('provider', $gateway->provider) }}" class="form-control" placeholder="e.g. mpesa, stripe">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Public Key</label>
                        <input type="text" name="public_key" value="{{ old('public_key', $gateway->public_key) }}" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Secret Key</label>
                        <input type="text" name="secret_key" value="{{ old('secret_key', $gateway->secret_key) }}" class="form-control">
                    </div>
                    <div class="col-md-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="enabled" name="enabled" {{ $gateway->enabled ? 'checked' : '' }}>
                            <label class="form-check-label" for="enabled">Enable Gateway</label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <button class="admin-action-btn" type="submit">Save</button>
                    </div>
                </div>
            </form>
            <div class="alert alert-warning mt-3">Do not commit real keys to git. Use environment variables in production.</div>
        </div>
    </div>
</div>
@endsection
