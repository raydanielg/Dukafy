@extends('admin.layouts.app')

@section('page_title', 'Customers')

@section('content')
<div class="admin-page">
    <div class="admin-page-head">
        <div class="admin-page-title">Customers</div>
        <div class="admin-page-actions">
            <a href="{{ route('admin.business_data.customers.create') }}" class="admin-action-btn">New Customer</a>
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
                            <th>Blacklist</th>
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
                                <td>{!! $c->is_blacklisted ? '<span class="badge bg-danger">Yes</span>' : '<span class="badge bg-success">No</span>' !!}</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.business_data.customers.edit', $c->id) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <form method="POST" action="{{ route('admin.business_data.customers.destroy', $c->id) }}" class="d-inline" onsubmit="return confirm('Delete this customer?');">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">No customers yet.</td>
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
