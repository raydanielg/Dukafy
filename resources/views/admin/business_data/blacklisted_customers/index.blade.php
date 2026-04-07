@extends('admin.layouts.app')

@section('page_title', 'Blacklisted Customers')

@section('content')
<div class="admin-page">
    <div class="admin-page-head">
        <div class="admin-page-title">Blacklisted Customers</div>
        <div class="admin-page-actions">
            <a href="{{ route('admin.business_data.customers.index') }}" class="admin-action-btn admin-action-btn-ghost">All Customers</a>
        </div>
    </div>

    <div class="admin-panel">
        <div class="admin-panel-body">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Business</th>
                            <th>Group</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $c)
                            <tr>
                                <td class="fw-semibold">{{ $c->name }}</td>
                                <td>{{ $c->business_name ?? '—' }}</td>
                                <td>{{ $c->group_name ?? '—' }}</td>
                                <td>{{ $c->phone ?? '—' }}</td>
                                <td>{{ $c->email ?? '—' }}</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.business_data.customers.edit', $c->id) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">No blacklisted customers.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $customers->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
