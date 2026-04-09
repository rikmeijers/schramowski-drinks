@extends('shared.layout')

@section('customSeoTags')
    <meta name="description" content="Setze dein Passwort zurück, indem du ein neues Passwort eingibst.">
    <meta name="keywords" content="passwort zurücksetzen, neues passwort, sicherheit, {{ config('app.name', 'App') }}">
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
            <h1 class="title mb-1">Passwort zurücksetzen</h1>
            <p class="text-body-secondary">Gib dein neues Passwort ein.</p>
        </div>

        @if(session('success'))
            <div class="text-success text-center mb-3">{{ session('success') }}</div>
        @endif

        <div class="card">
            <form method="POST" action="{{ route('password.reset') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="mb-3">
                    <label for="password" class="form-label">Neues Passwort</label>
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
                    <label for="password_confirmation" class="form-label">Neues Passwort wiederholen</label>
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
                        <i class="bi bi-key me-2"></i> Passwort speichern
                    </button>
                </div>
            </form>

        </div>

        <div class="mt-3 text-center small text-body-tertiary">
            &copy; {{ \Carbon\Carbon::now()->year }} {{ config('app.name', 'App') }}  v{{ config('app.version', '1.0.0') }}.
        </div>
    </div>
@endsection
