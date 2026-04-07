@extends('admin.layouts.app')

@section('page_title', 'Sales')

@section('content')
<div class="admin-page">
    <div class="admin-page-head">
        <div class="admin-page-title">Sales</div>
        <div class="admin-page-actions">
            <a href="{{ route('admin.business_data.sales.create') }}" class="admin-action-btn">New Sale</a>
        </div>
    </div>

    <div class="admin-panel">
        <div class="admin-panel-body">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Sale No</th>
                            <th>Sold At</th>
                            <th>Business</th>
                            <th>User</th>
                            <th>Customer</th>
                            <th>Total</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sales as $s)
                            <tr>
                                <td class="fw-semibold">{{ $s->sale_no }}</td>
                                <td>{{ \Carbon\Carbon::parse($s->sold_at)->format('M d, Y H:i') }}</td>
                                <td>{{ $s->business_name ?? '—' }}</td>
                                <td>{{ $s->user_name ?? '—' }}</td>
                                <td>{{ $s->customer_name ?? '—' }}</td>
                                <td>{{ number_format((float) $s->total, 2) }}</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.business_data.sales.show', $s->id) }}" class="btn btn-sm btn-outline-secondary">View</a>
                                    <form method="POST" action="{{ route('admin.business_data.sales.destroy', $s->id) }}" class="d-inline" onsubmit="return confirm('Delete this sale?');">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">No sales yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $sales->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
