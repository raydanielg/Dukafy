@extends('admin.layouts.app')

@section('page_title', 'Modules Manager')

@section('content')
<div class="admin-page">
    <div class="admin-page-head">
        <div class="admin-page-title">Modules Manager</div>
        <div class="admin-page-actions">
            <a href="{{ route('admin.extras.modules.create') }}" class="admin-action-btn">New Module</a>
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
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($modules as $m)
                            <tr>
                                <td class="fw-semibold">{{ $m->name }}</td>
                                <td class="text-muted">{{ $m->slug }}</td>
                                <td>{!! $m->enabled ? '<span class="badge bg-success">Enabled</span>' : '<span class="badge bg-secondary">Disabled</span>' !!}</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.extras.modules.edit', $m->id) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <form method="POST" action="{{ route('admin.extras.modules.destroy', $m->id) }}" class="d-inline" onsubmit="return confirm('Delete this module?');">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">No modules yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $modules->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
