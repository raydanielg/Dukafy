@extends('admin.layouts.app')

@section('page_title', 'SMS / WhatsApp Settings')

@section('content')
<div class="admin-page">
    <div class="admin-page-head">
        <div class="admin-page-title">SMS / WhatsApp Settings</div>
    </div>

    <div class="admin-panel">
        <div class="admin-panel-body">
            <form method="POST" action="{{ route('admin.system_settings.sms_whatsapp.update') }}">
                @csrf

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Provider</label>
                        <input name="provider" class="form-control" value="{{ old('provider', $settings['provider'] ?? '') }}" placeholder="Twilio, Beem, N-host...">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Sender ID</label>
                        <input name="sender_id" class="form-control" value="{{ old('sender_id', $settings['sender_id'] ?? '') }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">API Key</label>
                        <input name="api_key" type="password" class="form-control" value="{{ old('api_key', $settings['api_key'] ?? '') }}">
                    </div>

                    <div class="col-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="whatsapp_enabled" id="whatsapp_enabled" @checked(old('whatsapp_enabled', ($settings['whatsapp_enabled'] ?? '0') === '1'))>
                            <label class="form-check-label" for="whatsapp_enabled">Enable WhatsApp</label>
                        </div>
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
