@extends('admin.layouts.app')

@section('page_title', 'API Security')

@section('content')
<div class="admin-page animate__animated animate__fadeIn">
    <div class="admin-page-head">
        <div class="admin-page-title">API Security</div>
    </div>

    @if(session('new_api_key'))
        <div class="alert alert-success animate__animated animate__fadeInUp">
            <div class="fw-bold mb-1">New API Key (copy now)</div>
            <code>{{ session('new_api_key') }}</code>
        </div>
    @endif

    <div class="admin-grid">
        <div class="admin-panel">
            <div class="admin-panel-head">
                <div class="admin-panel-title">API Base URL</div>
            </div>
            <div class="admin-panel-body">
                <form method="POST" action="{{ route('admin.security.api_security.base_url') }}">
                    @csrf
                    <div class="row g-2">
                        <div class="col-md-9">
                            <input type="text" name="api_base_url" class="form-control" value="{{ old('api_base_url', $apiBaseUrl ?? '') }}" placeholder="https://your-domain.com/api" required>
                        </div>
                        <div class="col-md-3">
                            <button class="admin-action-btn" type="submit">Save</button>
                        </div>
                    </div>
                </form>

                <div class="text-muted mt-3" style="font-size: 13px;">
                    Recommended local values:
                    <div><strong>Android emulator:</strong> <code>http://10.0.2.2:8000/api</code></div>
                    <div><strong>Real device (WiFi):</strong> <code>http://YOUR_PC_IP:8000/api</code></div>
                </div>
            </div>
        </div>

        <div class="admin-panel">
            <div class="admin-panel-head">
                <div class="admin-panel-title">Create API Key</div>
            </div>
            <div class="admin-panel-body">
                <form method="POST" action="{{ route('admin.security.api_security.create') }}">
                    @csrf
                    <div class="row g-2">
                        <div class="col-md-8">
                            <input type="text" name="name" class="form-control" placeholder="Key name" required>
                        </div>
                        <div class="col-md-4">
                            <button class="admin-action-btn" type="submit">Generate</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="admin-panel">
            <div class="admin-panel-head">
                <div class="admin-panel-title">Keys</div>
            </div>
            <div class="admin-panel-body">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Status</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($keys as $key)
                                <tr class="animate__animated animate__fadeInUp">
                                    <td class="fw-semibold">{{ $key->name }}</td>
                                    <td>
                                        @if($key->revoked_at)
                                            <span class="badge bg-secondary">Revoked</span>
                                        @else
                                            <span class="badge bg-success">Active</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if(!$key->revoked_at)
                                            <form method="POST" action="{{ route('admin.security.api_security.revoke', $key->id) }}" class="d-inline">
                                                @csrf
                                                <button class="btn btn-sm btn-outline-danger" type="submit">Revoke</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">No keys yet.</td>
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
