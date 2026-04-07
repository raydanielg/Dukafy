@extends('admin.layouts.app')

@section('page_title', 'Access Logs')

@section('content')
<div class="admin-page">
    <div class="admin-page-head">
        <div class="admin-page-title">Access Logs</div>
        <div class="admin-page-actions">
            <a href="{{ route('admin.logs.errors') }}" class="admin-action-btn admin-action-btn-ghost">Error Logs</a>
        </div>
    </div>

    <div class="admin-panel">
        <div class="admin-panel-body">
            @if(!$log['exists'])
                <div class="admin-empty">Log file not found: {{ $log['path'] }}</div>
            @else
                <div class="text-muted mb-2" style="font-size: 13px;">File: {{ $log['path'] }} (showing last {{ count($log['lines']) }} lines)</div>
                <pre class="p-3" style="background:#0b1020;color:#d6e0ff;border-radius:10px;max-height:540px;overflow:auto;">{{ implode("\n", $log['lines']) }}</pre>
            @endif
        </div>
    </div>
</div>
@endsection
