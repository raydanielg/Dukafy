@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h4 mb-2">Admin Panel</h2>
            <p class="mb-3">Karibu, {{ auth()->user()->name }}.</p>

            <div class="row g-3">
                <div class="col-md-4">
                    <div class="border rounded p-3 h-100">
                        <div class="fw-bold">Users</div>
                        <div class="text-muted">Manage users and permissions.</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border rounded p-3 h-100">
                        <div class="fw-bold">Content</div>
                        <div class="text-muted">Manage landing articles and categories.</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border rounded p-3 h-100">
                        <div class="fw-bold">Settings</div>
                        <div class="text-muted">System configuration.</div>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <a href="{{ route('home') }}" class="btn btn-outline-secondary">Go to Dashboard</a>
            </div>
        </div>
    </div>
</div>
@endsection
