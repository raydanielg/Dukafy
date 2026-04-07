@extends('admin.layouts.app')

@section('page_title', 'IP Whitelisting')

@section('content')
<div class="admin-page animate__animated animate__fadeIn">
    <div class="admin-page-head">
        <div class="admin-page-title">IP Whitelisting</div>
    </div>

    <div class="admin-grid">
        <div class="admin-panel">
            <div class="admin-panel-head">
                <div class="admin-panel-title">Add IP Address</div>
            </div>
            <div class="admin-panel-body">
                <form method="POST" action="{{ route('admin.security.ip_whitelisting.add') }}">
                    @csrf
                    <div class="row g-2">
                        <div class="col-md-6">
                            <input type="text" name="ip_address" class="form-control" placeholder="e.g. 197.250.10.10" required>
                        </div>
                        <div class="col-md-6">
                            <input type="text" name="label" class="form-control" placeholder="Label (optional)">
                        </div>
                        <div class="col-md-12">
                            <button class="admin-action-btn" type="submit">Save IP</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="admin-panel">
            <div class="admin-panel-head">
                <div class="admin-panel-title">Whitelisted IPs</div>
            </div>
            <div class="admin-panel-body">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>IP</th>
                                <th>Label</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ips as $ip)
                                <tr class="animate__animated animate__fadeInUp">
                                    <td class="fw-semibold">{{ $ip->ip_address }}</td>
                                    <td class="text-muted">{{ $ip->label ?? '—' }}</td>
                                    <td>
                                        @if($ip->enabled)
                                            <span class="badge bg-success">Enabled</span>
                                        @else
                                            <span class="badge bg-secondary">Disabled</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">No IPs added.</td>
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
