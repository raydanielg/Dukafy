@extends('admin.layouts.app')

@section('page_title', 'Sales by Business')

@section('content')
<div class="admin-page">
    <div class="admin-page-head">
        <div class="admin-page-title">Sales by Business</div>
    </div>

    <div class="admin-panel">
        <div class="admin-panel-body">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Business</th>
                            <th>Sales Count</th>
                            <th>Total Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rows as $r)
                            <tr>
                                <td class="fw-semibold">{{ $r->name }}</td>
                                <td>{{ $r->sales_count }}</td>
                                <td>{{ number_format((float) $r->total_amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">No data yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
