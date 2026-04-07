@extends('admin.layouts.app')

@section('page_title', 'New Customer')

@section('content')
<div class="admin-page">
    <div class="admin-page-head">
        <div class="admin-page-title">New Customer</div>
        <div class="admin-page-actions">
            <a href="{{ route('admin.business_data.customers.index') }}" class="admin-action-btn admin-action-btn-ghost">Back</a>
        </div>
    </div>

    <div class="admin-panel">
        <div class="admin-panel-body">
            <form method="POST" action="{{ route('admin.business_data.customers.store') }}">
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
                        <label class="form-label">Group</label>
                        <select name="group_id" class="form-select">
                            <option value="">None</option>
                            @foreach($groups as $g)
                                <option value="{{ $g->id }}" @selected(old('group_id') == $g->id)>{{ $g->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Name</label>
                        <input name="name" class="form-control" value="{{ old('name') }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Phone</label>
                        <input name="phone" class="form-control" value="{{ old('phone') }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input name="email" type="email" class="form-control" value="{{ old('email') }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Address</label>
                        <input name="address" class="form-control" value="{{ old('address') }}">
                    </div>

                    <div class="col-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_blacklisted" id="is_blacklisted" @checked(old('is_blacklisted'))>
                            <label class="form-check-label" for="is_blacklisted">Blacklisted</label>
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
