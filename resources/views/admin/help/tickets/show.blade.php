@extends('admin.layouts.app')

@section('page_title', 'Ticket #' . (1000 + $ticket->id))

@section('content')
<div class="admin-page">
    <div class="admin-page-head">
        <div class="admin-page-title">
            <span class="text-muted">Ticket #{{ 1000 + $ticket->id }}:</span> {{ $ticket->subject }}
        </div>
        <div class="admin-page-actions">
            <a href="{{ route('admin.help.tickets') }}" class="admin-action-btn admin-action-btn-ghost">Back to List</a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <!-- Message Thread -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    @foreach($messages as $msg)
                        <div class="d-flex mb-4 {{ $msg->is_admin_reply ? 'justify-content-end' : '' }}">
                            <div class="p-3 rounded-4 {{ $msg->is_admin_reply ? 'bg-primary text-white' : 'bg-light' }}" style="max-width: 80%;">
                                <div class="small fw-bold mb-1">{{ $msg->user_name }} {{ $msg->is_admin_reply ? '(Admin)' : '' }}</div>
                                <div class="mb-1">{{ $msg->message }}</div>
                                <div class="text-xs opacity-75">{{ \Carbon\Carbon::parse($msg->created_at)->format('M d, H:i') }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="card-footer bg-white p-4">
                    <form method="POST" action="{{ route('admin.help.tickets.reply', $ticket->id) }}">
                        @csrf
                        <div class="mb-3">
                            <textarea name="message" class="form-control" rows="3" placeholder="Type your reply here..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary px-4">Send Reply</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="admin-panel">
                <div class="admin-panel-head"><div class="admin-panel-title">Ticket Details</div></div>
                <div class="admin-panel-body">
                    <ul class="list-group list-group-flush small">
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Status</span>
                            <span class="badge bg-primary">{{ ucfirst($ticket->status) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Priority</span>
                            <span class="badge bg-warning">{{ ucfirst($ticket->priority) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Created</span>
                            <span>{{ \Carbon\Carbon::parse($ticket->created_at)->format('M d, Y') }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .text-xs { font-size: 0.7rem; }
</style>
@endsection
