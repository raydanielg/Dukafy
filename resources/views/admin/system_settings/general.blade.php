@extends('admin.layouts.app')

@section('page_title', 'General Settings')

@section('content')
<div class="admin-page">
    <div class="admin-page-head">
        <div class="admin-page-title">General Settings</div>
    </div>

    <div class="admin-panel">
        <div class="admin-panel-body">
            <form method="POST" action="{{ route('admin.system_settings.general.update') }}">
                @csrf

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">App Name</label>
                        <input name="app_name" class="form-control" value="{{ old('app_name', $settings['app_name'] ?? '') }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Timezone</label>
                        <input name="timezone" class="form-control" value="{{ old('timezone', $settings['timezone'] ?? '') }}" placeholder="Africa/Dar_es_Salaam">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Default Currency</label>
                        <input name="default_currency" class="form-control" value="{{ old('default_currency', $settings['default_currency'] ?? '') }}" placeholder="TZS">
                    </div>

                    <div class="col-12">
                        <button class="admin-action-btn" type="submit">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
