@extends('admin.layouts.app')

@section('page_title', 'Customer Groups')

@section('content')
<div class="admin-page">
    <div class="admin-page-head">
        <div class="admin-page-title">Customer Groups</div>
        <div class="admin-page-actions">
            <a href="{{ route('admin.business_data.customer_groups.create') }}" class="admin-action-btn">New Group</a>
        </div>
    </div>

    <div class="admin-panel">
        <div class="admin-panel-body">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Business</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($groups as $g)
                            <tr>
                                <td class="fw-semibold">{{ $g->name }}</td>
                                <td class="text-muted">{{ $g->slug }}</td>
                                <td>{{ $g->business_name ?? '—' }}</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.business_data.customer_groups.edit', $g->id) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <form method="POST" action="{{ route('admin.business_data.customer_groups.destroy', $g->id) }}" class="d-inline" onsubmit="return confirm('Delete this group?');">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">No groups yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $groups->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
