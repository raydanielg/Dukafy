@extends('admin.layouts.app')

@section('page_title', 'Plans')

@section('content')
<div class="admin-page animate__animated animate__fadeIn">
    <div class="admin-page-head">
        <div class="admin-page-title">Plans</div>
        <div class="admin-page-actions">
            <a href="{{ route('admin.subscription.plans.create') }}" class="admin-action-btn">Add New Plan</a>
        </div>
    </div>

    <div class="admin-panel">
        <div class="admin-panel-body">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Monthly</th>
                            <th>Yearly</th>
                            <th>Status</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($plans as $plan)
                            <tr class="animate__animated animate__fadeInUp">
                                <td class="fw-semibold">{{ $plan->name }}</td>
                                <td class="text-muted">TZS {{ number_format($plan->price_monthly) }}</td>
                                <td class="text-muted">TZS {{ number_format($plan->price_yearly) }}</td>
                                <td>
                                    @if($plan->active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Disabled</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.subscription.plans.edit', $plan->id) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">No plans.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
