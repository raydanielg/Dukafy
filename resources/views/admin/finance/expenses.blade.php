@extends('admin.layouts.app')

@section('page_title', 'Expenses')

@section('content')
<div class="admin-page animate__animated animate__fadeIn">
    <div class="admin-page-head">
        <div class="admin-page-title">Expenses</div>
    </div>

    <div class="admin-grid">
        <div class="admin-panel">
            <div class="admin-panel-head">
                <div class="admin-panel-title">Add Expense</div>
            </div>
            <div class="admin-panel-body">
                <form method="POST" action="{{ route('admin.finance.expenses.store') }}">
                    @csrf
                    <div class="row g-2">
                        <div class="col-md-6">
                            <input type="text" name="title" class="form-control" placeholder="Title" required>
                        </div>
                        <div class="col-md-6">
                            <input type="number" min="0" name="amount" class="form-control" placeholder="Amount (TZS)" required>
                        </div>
                        <div class="col-md-6">
                            <input type="text" name="category" class="form-control" placeholder="Category">
                        </div>
                        <div class="col-md-6">
                            <input type="date" name="spent_at" class="form-control" value="{{ now()->toDateString() }}">
                        </div>
                        <div class="col-md-12">
                            <textarea name="notes" rows="2" class="form-control" placeholder="Notes"></textarea>
                        </div>
                        <div class="col-md-12">
                            <button class="admin-action-btn" type="submit">Save Expense</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="admin-panel">
            <div class="admin-panel-head">
                <div class="admin-panel-title">Recent Expenses</div>
            </div>
            <div class="admin-panel-body">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Amount</th>
                                <th class="text-end">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($expenses as $e)
                                <tr class="animate__animated animate__fadeInUp">
                                    <td class="fw-semibold">{{ $e->title }}</td>
                                    <td class="text-muted">{{ $e->category ?? '—' }}</td>
                                    <td class="text-muted">TZS {{ number_format($e->amount) }}</td>
                                    <td class="text-end text-muted">{{ $e->spent_at ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">No expenses.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $expenses->links() }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
