@extends('admin.layouts.app')

@section('page_title', 'Sale Details')

@section('content')
<div class="admin-page">
    <div class="admin-page-head">
        <div class="admin-page-title">Sale {{ $sale->sale_no }}</div>
        <div class="admin-page-actions">
            <a href="{{ route('admin.business_data.sales.index') }}" class="admin-action-btn admin-action-btn-ghost">Back</a>
        </div>
    </div>

    <div class="admin-grid">
        <div class="admin-panel">
            <div class="admin-panel-head">
                <div class="admin-panel-title">Summary</div>
            </div>
            <div class="admin-panel-body">
                <div class="row g-2">
                    <div class="col-md-6"><strong>Business:</strong> {{ $sale->business_name ?? '—' }}</div>
                    <div class="col-md-6"><strong>User:</strong> {{ $sale->user_name ?? '—' }}</div>
                    <div class="col-md-6"><strong>Customer:</strong> {{ $sale->customer_name ?? '—' }}</div>
                    <div class="col-md-6"><strong>Sold At:</strong> {{ \Carbon\Carbon::parse($sale->sold_at)->format('M d, Y H:i') }}</div>
                    <div class="col-md-6"><strong>Subtotal:</strong> {{ number_format((float) $sale->subtotal, 2) }}</div>
                    <div class="col-md-6"><strong>Tax:</strong> {{ number_format((float) $sale->tax, 2) }}</div>
                    <div class="col-md-6"><strong>Discount:</strong> {{ number_format((float) $sale->discount, 2) }}</div>
                    <div class="col-md-6"><strong>Total:</strong> {{ number_format((float) $sale->total, 2) }}</div>
                </div>
            </div>
        </div>

        <div class="admin-panel">
            <div class="admin-panel-head">
                <div class="admin-panel-title">Items</div>
            </div>
            <div class="admin-panel-body">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>SKU</th>
                                <th>Qty</th>
                                <th>Unit Price</th>
                                <th>Line Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $i)
                                <tr>
                                    <td class="fw-semibold">{{ $i->product_name }}</td>
                                    <td class="text-muted">{{ $i->sku ?? '—' }}</td>
                                    <td>{{ $i->qty }}</td>
                                    <td>{{ number_format((float) $i->unit_price, 2) }}</td>
                                    <td>{{ number_format((float) $i->line_total, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
