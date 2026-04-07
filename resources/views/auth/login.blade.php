@extends('layouts.app')

@section('content')
<div class="auth-shell">
    <div class="auth-card">
        <div class="auth-left">
            <div class="auth-left-inner">
                <div class="auth-brand">Dukafy</div>
                <h1 class="auth-title">Simamia Biashara Yako</h1>
                <p class="auth-subtitle">Mfumo wa mauzo, bidhaa, wateja, na ripoti kwa biashara ndogo na ya kati.</p>
                <p class="auth-tagline">Haraka. Salama. Kisasa.</p>
            </div>
        </div>

        <div class="auth-right">
            <div class="auth-right-top">
                <a href="{{ url('/') }}" class="auth-back">Go Back</a>
            </div>

            <div class="auth-form-wrap">
                <div class="auth-form-brand">DUKAFY</div>
                <h2 class="auth-form-title">Ingia</h2>

                <form method="POST" action="{{ route('login') }}" class="auth-form">
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label">Jina la mtumiaji / Barua pepe</label>
                        <input id="email" type="email" class="form-control auth-input @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Nenosiri</label>
                        <input id="password" type="password" class="form-control auth-input @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember">
                                {{ __('Remember Me') }}
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn auth-submit w-100">
                        <span class="btn-text">Ingia</span>
                        <span class="btn-spinner" aria-hidden="true"></span>
                    </button>

                    @if (Route::has('password.request'))
                        <div class="text-center mt-3">
                            <a class="auth-link" href="{{ route('password.request') }}">Umesahau nenosiri?</a>
                        </div>
                    @endif

                    <div class="text-center mt-3">
                        <span class="text-muted">Bado hujisajili? </span>
                        <a class="auth-link" href="{{ url('/') }}">Wasiliana nasi</a>
                    </div>

                    <div class="auth-footer">© {{ date('Y') }} Dukafy. All Rights Reserved.</div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
