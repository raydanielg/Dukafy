@extends('admin.layouts.app')

@section('page_title', 'System Health')

@section('content')
<div class="admin-page">
    <div class="admin-page-head">
        <div class="admin-page-title">System Health</div>
        <div class="admin-page-actions">
            <form method="POST" action="{{ route('admin.system_settings.clear_cache') }}" onsubmit="return confirm('Clear cache now?');">
                @csrf
                <button class="btn btn-outline-danger" type="submit">Clear Cache</button>
            </form>
        </div>
    </div>

    <div class="admin-panel">
        <div class="admin-panel-body">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Key</th>
                            <th>Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($health as $k => $v)
                            <tr>
                                <td class="text-muted">{{ $k }}</td>
                                <td class="fw-semibold">{{ is_bool($v) ? ($v ? 'true' : 'false') : $v }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
