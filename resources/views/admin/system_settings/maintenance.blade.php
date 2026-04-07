@extends('admin.layouts.app')

@section('page_title', 'Maintenance Mode')

@section('content')
<div class="admin-page">
    <div class="admin-page-head">
        <div class="admin-page-title">Maintenance Mode</div>
    </div>

    <div class="admin-panel">
        <div class="admin-panel-body">
            <form method="POST" action="{{ route('admin.system_settings.maintenance.update') }}">
                @csrf

                <div class="row g-3">
                    <div class="col-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="maintenance_enabled" id="maintenance_enabled" @checked(old('maintenance_enabled', ($settings['maintenance_enabled'] ?? '0') === '1'))>
                            <label class="form-check-label" for="maintenance_enabled">Enable Maintenance Mode</label>
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Maintenance Message</label>
                        <input name="maintenance_message" class="form-control" value="{{ old('maintenance_message', $settings['maintenance_message'] ?? '') }}">
                    </div>

                    <div class="col-12">
                        <button class="admin-action-btn" type="submit">Save</button>
                    </div>
                </div>
            </form>

            <form method="POST" action="{{ route('admin.system_settings.clear_cache') }}" class="mt-4" onsubmit="return confirm('Clear cache now?');">
                @csrf
                <button class="btn btn-outline-danger" type="submit">Clear Cache</button>
            </form>
        </div>
    </div>
</div>
@endsection
