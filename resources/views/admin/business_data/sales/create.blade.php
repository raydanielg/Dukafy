@extends('admin.layouts.app')

@section('page_title', 'New Sale')

@section('content')
<div class="admin-page">
    <div class="admin-page-head">
        <div class="admin-page-title">New Sale</div>
        <div class="admin-page-actions">
            <a href="{{ route('admin.business_data.sales.index') }}" class="admin-action-btn admin-action-btn-ghost">Back</a>
        </div>
    </div>

    <div class="admin-panel">
        <div class="admin-panel-body">
            <form method="POST" action="{{ route('admin.business_data.sales.store') }}">
                @csrf

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Business</label>
                        <select name="business_id" class="form-select" required>
                            <option value="">Select business</option>
                            @foreach($businesses as $b)
                                <option value="{{ $b->id }}" @selected(old('business_id') == $b->id)>{{ $b->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">User</label>
                        <select name="user_id" class="form-select">
                            <option value="">None</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}" @selected(old('user_id') == $u->id)>{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Customer</label>
                        <select name="customer_id" class="form-select">
                            <option value="">None</option>
                            @foreach($customers as $c)
                                <option value="{{ $c->id }}" @selected(old('customer_id') == $c->id)>{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Sale No (optional)</label>
                        <input name="sale_no" class="form-control" value="{{ old('sale_no') }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Sold At</label>
                        <input type="datetime-local" name="sold_at" class="form-control" value="{{ old('sold_at', now()->format('Y-m-d\TH:i')) }}" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Payment Method</label>
                        <input name="payment_method" class="form-control" value="{{ old('payment_method') }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Tax</label>
                        <input type="number" step="0.01" min="0" name="tax" class="form-control" value="{{ old('tax', 0) }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Discount</label>
                        <input type="number" step="0.01" min="0" name="discount" class="form-control" value="{{ old('discount', 0) }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Notes</label>
                        <input name="notes" class="form-control" value="{{ old('notes') }}">
                    </div>

                    <div class="col-12">
                        <div class="mb-2 fw-semibold">Items (fill at least 1)</div>
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th style="width: 35%;">Product</th>
                                        <th style="width: 20%;">SKU</th>
                                        <th style="width: 10%;">Qty</th>
                                        <th style="width: 15%;">Unit Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @for($i = 0; $i < 5; $i++)
                                        <tr>
                                            <td>
                                                <select name="items[{{ $i }}][product_id]" class="form-select">
                                                    <option value="">Custom / None</option>
                                                    @foreach($products as $p)
                                                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                                                    @endforeach
                                                </select>
                                                <input class="form-control mt-2" name="items[{{ $i }}][product_name]" placeholder="Product name" value="{{ old('items.' . $i . '.product_name') }}">
                                            </td>
                                            <td>
                                                <input class="form-control" name="items[{{ $i }}][sku]" value="{{ old('items.' . $i . '.sku') }}">
                                            </td>
                                            <td>
                                                <input type="number" min="1" class="form-control" name="items[{{ $i }}][qty]" value="{{ old('items.' . $i . '.qty', 1) }}">
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" min="0" class="form-control" name="items[{{ $i }}][unit_price]" value="{{ old('items.' . $i . '.unit_price', 0) }}">
                                            </td>
                                        </tr>
                                    @endfor
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="col-12">
                        <button class="admin-action-btn" type="submit">Save Sale</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
