@extends('shared.layout')

@section('customSeoTags')
    <meta name="description" content="Gib deine E-Mail-Adresse ein, um einen Link zum Zurücksetzen des Passworts zu erhalten.">
    <meta name="keywords" content="passwort vergessen, passwort zurücksetzen, e-mail, sicherheit, {{ config('app.name', 'App') }}">
@endsection

@section('customStyles')
    <link rel="stylesheet" href="{{ url('/assets/css/auth.css') }}">
@endsection

@section('content')
    <div class="auth-content">
        <div class="text-center mb-4">
            <a href="{{ route('login.form') }}">
                <img class="logo" src="{{ url('/assets/images/branding/icon.png') }}" alt="Logo">
            </a>
            <h1 class="title mb-1">Anmelden</h1>
            <p class="text-body-secondary">Gib deine E-Mail-Adresse und dein Passwort ein, um dich anzumelden.</p>
        </div>

        @if(session('success'))
            <div class="text-success text-center mb-3">{{ session('success') }}</div>
            @php
                session()->forget('success');
            @endphp
        @endif

        @if(session('error'))
            <div class="text-danger text-center mb-3">{{ session('error') }}</div>
        @endif

        @if(session('error-verify'))
            <div class="alert alert-warning bg-warning text-warning-emphasis default-rounded text-center mb-3 p-3 rounded shadow-sm" role="alert">
                <i class="bi bi-exclamation-triangle-fill fs-2 mb-2"></i>
                <div class="mb-2 fw-semibold">
                    {{ session('error-verify') }}
                </div>
                <form method="GET" action="{{ url('/email/resend') }}" class="mt-2 text-center">
                    @csrf
                    <button type="submit" class="btn btn-warning default-rounded btn-sm">
                        Bestätigungs-E-Mail erneut senden
                    </button>
                </form>
            </div>
        @endif

        <div class="card">
            <div class="row text-center mb-4">
                <div class="col-12 border-bottom border-2 border-primary">
                    <a href="{{ url('/login') }}" class="auth-btn-active btn btn-link text-decoration-none text-primary" style="font-size:1.25rem;">Anmelden</a>
                </div>
            </div>

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label">E-Mail-Adresse</label>
                    <input type="email"
                           class="form-control @error('email') is-invalid @enderror"
                           id="email"
                           name="email"
                           value="{{ old('email') }}"
                           required>
                    @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Passwort</label>
                    <input type="password"
                           class="form-control @error('password') is-invalid @enderror"
                           id="password"
                           name="password"
                           required>
                    @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>


                <div class="mb-3 d-flex justify-content-between align-items-center">
                    @if(\App\Helpers\CookieConsent::accepted())
                        <div class="form-check">
                            <input class="form-check-input me-2" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember">Angemeldet bleiben</label>
                        </div>
                    @else
                        <input class="d-none" type="checkbox" name="remember" id="remember">
                    @endif
                    <a href="{{ route('password.forgot.form') }}" class="text-decoration-none">Passwort vergessen?</a>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-box-arrow-in-right me-2"></i> Anmelden
                    </button>
                </div>
            </form>
        </div>

        <div class="mt-3 text-center small text-body-tertiary">
            &copy; {{ \Carbon\Carbon::now()->year }} {{ config('app.name', 'App') }}  v{{ config('app.version', '1.0.0') }}.
        </div>
    </div>
@endsection
