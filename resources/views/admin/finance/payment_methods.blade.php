@extends('admin.layouts.app')

@section('page_title', 'Payment Methods')

@section('content')
<div class="admin-page animate__animated animate__fadeIn">
    <div class="admin-page-head">
        <div class="admin-page-title">Payment Methods</div>
    </div>

    <div class="admin-grid">
        <div class="admin-panel">
            <div class="admin-panel-head">
                <div class="admin-panel-title">Add Method</div>
            </div>
            <div class="admin-panel-body">
                <form method="POST" action="{{ route('admin.finance.payment_methods.store') }}">
                    @csrf
                    <div class="row g-2">
                        <div class="col-md-6">
                            <input type="text" name="name" class="form-control" placeholder="Name" required>
                        </div>
                        <div class="col-md-6">
                            <input type="text" name="slug" class="form-control" placeholder="Slug" required>
                        </div>
                        <div class="col-md-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="enabled" name="enabled" checked>
                                <label class="form-check-label" for="enabled">Enabled</label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <button class="admin-action-btn" type="submit">Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="admin-panel">
            <div class="admin-panel-head">
                <div class="admin-panel-title">Methods</div>
            </div>
            <div class="admin-panel-body">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Slug</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($methods as $m)
                                <tr class="animate__animated animate__fadeInUp">
                                    <td class="fw-semibold">{{ $m->name }}</td>
                                    <td class="text-muted">{{ $m->slug }}</td>
                                    <td>
                                        @if($m->enabled)
                                            <span class="badge bg-success">Enabled</span>
                                        @else
                                            <span class="badge bg-secondary">Disabled</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">No methods yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
