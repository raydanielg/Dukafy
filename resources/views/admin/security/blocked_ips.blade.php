@extends('admin.layouts.app')

@section('page_title', 'Blocked IPs')

@section('content')
<div class="admin-page animate__animated animate__fadeIn">
    <div class="admin-page-head">
        <div class="admin-page-title">Blocked IPs</div>
    </div>

    <div class="admin-grid">
        <div class="admin-panel">
            <div class="admin-panel-head">
                <div class="admin-panel-title">Block New IP</div>
            </div>
            <div class="admin-panel-body">
                <form method="POST" action="{{ route('admin.security.blocked_ips.add') }}">
                    @csrf
                    <div class="row g-2">
                        <div class="col-md-6">
                            <input type="text" name="ip_address" class="form-control" placeholder="e.g. 10.10.10.10" required>
                        </div>
                        <div class="col-md-6">
                            <input type="text" name="reason" class="form-control" placeholder="Reason (optional)">
                        </div>
                        <div class="col-md-12">
                            <button class="admin-action-btn" type="submit">Block IP</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="admin-panel">
            <div class="admin-panel-head">
                <div class="admin-panel-title">Blocked List</div>
            </div>
            <div class="admin-panel-body">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>IP</th>
                                <th>Reason</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ips as $ip)
                                <tr class="animate__animated animate__fadeInUp">
                                    <td class="fw-semibold">{{ $ip->ip_address }}</td>
                                    <td class="text-muted">{{ $ip->reason ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center text-muted py-4">No blocked IPs.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
