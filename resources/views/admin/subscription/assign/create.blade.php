@extends('admin.layouts.app')

@section('page_title', 'Assign Plan to User')

@section('content')
<div class="admin-page animate__animated animate__fadeIn">
    <div class="admin-page-head">
        <div class="admin-page-title">Assign Plan to User</div>
        <div class="admin-page-actions">
            <a href="{{ route('admin.subscription.subscriptions') }}" class="admin-action-btn admin-action-btn-ghost">Back</a>
        </div>
    </div>

    <div class="admin-panel">
        <div class="admin-panel-body">
            <form method="POST" action="{{ route('admin.subscription.assign.store') }}" class="admin-form">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">User</label>
                        <select name="user_id" class="form-select" required>
                            <option value="">—</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Plan</label>
                        <select name="plan_id" class="form-select" required>
                            <option value="">—</option>
                            @foreach($plans as $p)
                                <option value="{{ $p->id }}">{{ $p->name }} (TZS {{ number_format($p->price_monthly) }}/mo)</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="active">Active</option>
                            <option value="trial">Trial</option>
                            <option value="expired">Expired</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Start Date</label>
                        <input type="date" name="starts_at" class="form-control" value="{{ now()->toDateString() }}" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">End Date</label>
                        <input type="date" name="ends_at" class="form-control" value="{{ now()->addMonth()->toDateString() }}" required>
                    </div>

                    <div class="col-md-12">
                        <button class="admin-action-btn" type="submit">Assign Plan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
