@extends('shared.layout')

@section('customSeoTags')
    <meta name="description" content="Erstelle ein neues Konto, um Zugriff auf alle Funktionen von {{ config('app.name', 'App') }} zu erhalten.">
    <meta name="keywords" content="registrieren, konto erstellen, neues konto, {{ config('app.name', 'App') }}, sicherheit">
@endsection

@section('customStyles')
    <link rel="stylesheet" href="{{ url('/assets/css/auth.css') }}">
@endsection

@section('content')
    <div class="auth-content">
        <div class="mb-3">
            <a href="{{ route('users.index') }}" class="text-decoration-none">
                ← Zurück
            </a>
        </div>

        <div class="text-center mb-4">
            <a href="{{ route('home') }}">
                <img class="logo" src="{{ url('/assets/images/branding/icon.png') }}" alt="Logo">
            </a>

            <h1 class="title mb-1 mt-3">Benutzer hinzufügen</h1>
            <p class="text-body-secondary">Nur Admins können neue Benutzer hinzufügen.</p>
        </div>

        @if(session('success'))
            <div class="text-success text-center mb-3">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="text-danger text-center mb-3">{{ session('error') }}</div>
        @endif

        <div class="card">
            <form method="POST" action="{{ url('/register') }}">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text"
                           class="form-control @error('name') is-invalid @enderror"
                           id="name"
                           name="name"
                           value="{{ old('name') }}"
                           required>
                    @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

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

                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Passwort wiederholen</label>
                    <input type="password"
                           class="form-control @error('password_confirmation') is-invalid @enderror"
                           id="password_confirmation"
                           name="password_confirmation"
                           required>
                    @error('password_confirmation')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-person-plus me-2"></i> Benutzer erstellen
                    </button>
                </div>
            </form>
        </div>

        <div class="mt-3 text-center small text-body-tertiary">
            &copy; {{ \Carbon\Carbon::now()->year }} {{ config('app.name', 'App') }}  v{{ config('app.version', '1.0.0') }}.
        </div>
    </div>
@endsection
