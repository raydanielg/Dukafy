@extends('admin.layouts.app')

@section('page_title', 'Invoices & Payments')

@section('content')
<div class="admin-page animate__animated animate__fadeIn">
    <div class="admin-page-head">
        <div class="admin-page-title">Invoices & Payments</div>
    </div>

    <div class="admin-grid">
        <div class="admin-panel">
            <div class="admin-panel-head">
                <div class="admin-panel-title">Invoices</div>
            </div>
            <div class="admin-panel-body">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th class="text-end">Issued</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($invoices as $inv)
                                <tr class="animate__animated animate__fadeInUp">
                                    <td>
                                        <div class="fw-semibold">{{ $inv->user_name ?? '—' }}</div>
                                        <div class="text-muted small">{{ $inv->user_email ?? '' }}</div>
                                    </td>
                                    <td class="text-muted">TZS {{ number_format($inv->amount) }}</td>
                                    <td><span class="badge bg-secondary">{{ ucfirst($inv->status) }}</span></td>
                                    <td class="text-end text-muted">{{ $inv->issued_at ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">No invoices.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $invoices->links() }}</div>
            </div>
        </div>

        <div class="admin-panel">
            <div class="admin-panel-head">
                <div class="admin-panel-title">Payments</div>
            </div>
            <div class="admin-panel-body">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Invoice</th>
                                <th>Amount</th>
                                <th>Method</th>
                                <th class="text-end">Paid At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payments as $pay)
                                <tr class="animate__animated animate__fadeInUp">
                                    <td class="text-muted">#{{ $pay->invoice_id }}</td>
                                    <td class="text-muted">TZS {{ number_format($pay->amount) }}</td>
                                    <td class="text-muted">{{ $pay->method ?? '—' }}</td>
                                    <td class="text-end text-muted">{{ $pay->paid_at ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">No payments.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $payments->links() }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
