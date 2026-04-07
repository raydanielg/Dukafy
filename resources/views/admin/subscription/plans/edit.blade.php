@extends('admin.layouts.app')

@section('page_title', 'Edit Plan')

@section('content')
@php
    $features = $plan->features ? implode("\n", json_decode($plan->features, true) ?? []) : '';
@endphp
<div class="admin-page animate__animated animate__fadeIn">
    <div class="admin-page-head">
        <div class="admin-page-title">Edit Plan</div>
        <div class="admin-page-actions">
            <a href="{{ route('admin.subscription.plans') }}" class="admin-action-btn admin-action-btn-ghost">Back</a>
        </div>
    </div>

    <div class="admin-panel">
        <div class="admin-panel-body">
            <form method="POST" action="{{ route('admin.subscription.plans.update', $plan->id) }}" class="admin-form">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $plan->name) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Slug</label>
                        <input type="text" name="slug" class="form-control" value="{{ old('slug', $plan->slug) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Monthly Price (TZS)</label>
                        <input type="number" min="0" name="price_monthly" class="form-control" value="{{ old('price_monthly', $plan->price_monthly) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Yearly Price (TZS)</label>
                        <input type="number" min="0" name="price_yearly" class="form-control" value="{{ old('price_yearly', $plan->price_yearly) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">User Limit</label>
                        <input type="number" min="1" name="user_limit" class="form-control" value="{{ old('user_limit', $plan->user_limit) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Product Limit</label>
                        <input type="number" min="1" name="product_limit" class="form-control" value="{{ old('product_limit', $plan->product_limit) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Trial Days</label>
                        <input type="number" min="0" name="trial_days" class="form-control" value="{{ old('trial_days', $plan->trial_days) }}">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Features (one per line)</label>
                        <textarea name="features" class="form-control" rows="5">{{ old('features', $features) }}</textarea>
                    </div>
                    <div class="col-md-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="active" name="active" {{ $plan->active ? 'checked' : '' }}>
                            <label class="form-check-label" for="active">Active</label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <button class="admin-action-btn" type="submit">Save Changes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
