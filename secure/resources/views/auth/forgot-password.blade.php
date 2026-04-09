@extends('shared.layout')

@section('customSeoTags')
    <meta name="description" content="Fordere einen Link zum Zurücksetzen deines Passworts an. Gib deine E-Mail-Adresse ein und erhalte Anweisungen zum Zurücksetzen.">
    <meta name="keywords" content="passwort vergessen, passwort zurücksetzen, konto, e-mail, sicherheit, {{ config('app.name', 'App') }}">
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
            <h1 class="title mb-1">Passwort vergessen</h1>
            <p class="text-body-secondary">Gib deine E-Mail-Adresse ein, um einen Reset-Link zu erhalten.</p>
        </div>

        @if(session('success'))
            <div class="text-success text-center mb-3">{{ session('success') }}</div>
        @endif

        <div class="card">
            <a href="{{ back()->getTargetUrl() }}" class="btn btn-link text-decoration-none position-absolute top-0 start-0 m-3">
                <i class="bi bi-arrow-left me-1"></i> Zurück
            </a>
            <br />

            <form method="POST" class="mt-3" action="{{ route('password.forgot') }}">
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

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-envelope me-2"></i> Reset-Link senden
                    </button>
                </div>
            </form>
        </div>

        <div class="mt-3 text-center small text-body-tertiary">
            &copy; {{ \Carbon\Carbon::now()->year }} {{ config('app.name', 'App') }}  v{{ config('app.version', '1.0.0') }}.
        </div>
    </div>
@endsection
