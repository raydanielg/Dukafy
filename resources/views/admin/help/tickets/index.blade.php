@extends('admin.layouts.app')

@section('page_title', 'Support Tickets')

@section('content')
<div class="admin-page">
    <div class="admin-page-head">
        <div class="admin-page-title">Support Tickets</div>
    </div>

    <div class="admin-panel">
        <div class="admin-panel-body">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Ticket ID</th>
                            <th>User</th>
                            <th>Subject</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Last Update</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tickets as $t)
                            <tr>
                                <td class="text-muted">#TK-{{ 1000 + $t->id }}</td>
                                <td class="fw-bold">{{ $t->user_name }}</td>
                                <td>{{ $t->subject }}</td>
                                <td>
                                    <span class="badge @if($t->priority == 'urgent') bg-danger @elseif($t->priority == 'high') bg-warning @else bg-info @endif">
                                        {{ ucfirst($t->priority) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge @if($t->status == 'open') bg-success @elseif($t->status == 'closed') bg-secondary @else bg-primary @endif">
                                        {{ str_replace('_', ' ', ucfirst($t->status)) }}
                                    </span>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($t->updated_at)->diffForHumans() }}</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.help.tickets.show', $t->id) }}" class="btn btn-sm btn-primary">Open Ticket</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">No support tickets found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $tickets->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
