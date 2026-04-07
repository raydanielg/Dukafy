@extends('admin.layouts.app')

@section('page_title', 'System Info')

@section('content')
<div class="admin-page">
    <div class="admin-page-head">
        <div class="admin-page-title">System Information & Diagnostics</div>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="admin-panel h-100">
                <div class="admin-panel-head"><div class="admin-panel-title">Application Environment</div></div>
                <div class="admin-panel-body">
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <tbody>
                                @foreach($info as $key => $val)
                                    @if(in_array($key, ['App Name', 'Laravel Version', 'Environment', 'Debug Mode', 'Timezone']))
                                        <tr>
                                            <td class="text-muted" style="width: 40%;">{{ $key }}</td>
                                            <td class="fw-bold">{{ $val }}</td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="admin-panel h-100">
                <div class="admin-panel-head"><div class="admin-panel-title">Server & PHP Config</div></div>
                <div class="admin-panel-body">
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <tbody>
                                @foreach($info as $key => $val)
                                    @if(!in_array($key, ['App Name', 'Laravel Version', 'Environment', 'Debug Mode', 'Timezone']))
                                        <tr>
                                            <td class="text-muted" style="width: 40%;">{{ $key }}</td>
                                            <td class="fw-bold">{{ $val }}</td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
