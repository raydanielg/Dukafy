@extends('admin.layouts.app')

@section('page_title', 'Announcements')

@section('content')
<div class="admin-page">
    <div class="admin-page-head">
        <div class="admin-page-title">Announcements</div>
        <div class="admin-page-actions">
            <a href="{{ route('admin.extras.announcements.create') }}" class="admin-action-btn">New Announcement</a>
        </div>
    </div>

    <div class="admin-panel">
        <div class="admin-panel-body">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Active</th>
                            <th>Published</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($announcements as $a)
                            <tr>
                                <td class="fw-semibold">{{ $a->title }}</td>
                                <td>{!! $a->is_active ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-secondary">No</span>' !!}</td>
                                <td>{{ $a->published_at ? \Carbon\Carbon::parse($a->published_at)->format('M d, Y') : '—' }}</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.extras.announcements.edit', $a->id) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <form method="POST" action="{{ route('admin.extras.announcements.destroy', $a->id) }}" class="d-inline" onsubmit="return confirm('Delete this announcement?');">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">No announcements yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $announcements->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
