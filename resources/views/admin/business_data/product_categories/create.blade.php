@extends('admin.layouts.app')

@section('page_title', 'New Category')

@section('content')
<div class="admin-page">
    <div class="admin-page-head">
        <div class="admin-page-title">New Product Category</div>
        <div class="admin-page-actions">
            <a href="{{ route('admin.business_data.product_categories.index') }}" class="admin-action-btn admin-action-btn-ghost">Back</a>
        </div>
    </div>

    <div class="admin-panel">
        <div class="admin-panel-body">
            <form method="POST" action="{{ route('admin.business_data.product_categories.store') }}">
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
                        <label class="form-label">Slug (optional)</label>
                        <input name="slug" class="form-control" value="{{ old('slug') }}">
                    </div>

                    <div class="col-12">
                        <label class="form-label">Name</label>
                        <input name="name" class="form-control" value="{{ old('name') }}" required>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
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
