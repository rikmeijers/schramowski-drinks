@extends('shared.layout')

@section('customSeoTags')
    <meta name="description" content="Verwalte und aktualisiere deine Kontoeinstellungen, einschließlich Profilinformationen und Passwortänderungen.">
    <meta name="keywords" content="konto einstellungen, profil aktualisieren, passwort ändern, sicherheit, {{ config('app.name', 'App') }}">
@endsection

@section('customStyles')
    <link rel="stylesheet" href="{{ url('/assets/css/settings.css') }}">
    <style>
        .nav-link.settings {
            background-color: var(--bs-primary);
            color: #fff;
        }

        .nav-link.settings:hover {
            background-color: var(--bs-primary);
            color: #fff !important;
            transform: none;
            cursor: default;
        }
    </style>
@endsection

@section('content')
    <div class="settings-container">
        @if(session('success'))
            <div class="alert text-success-emphasis bg-success alert-success alert-dismissible fade show settings-alert" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card settings-card mb-4">
            <div class="card-header settings-card-header">
                <h5 class="mb-0">Profil aktualisieren</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('settings.updateProfile') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" name="name"  id="name" value="{{ old('name', $user->name) }}" class="form-control @error('name') is-invalid @enderror">
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">E-Mail-Adresse</label>
                        <input type="email" name="email"  id="email" value="{{ old('email', $user->email) }}" class="form-control @error('email') is-invalid @enderror">
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <button type="submit" class="btn btn-primary settings-btn w-100">Speichern</button>
                </form>
            </div>
        </div>

        <div class="card settings-card">
            <div class="card-header settings-card-header">
                <h5 class="mb-0">Passwort ändern</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('settings.updatePassword') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="current_password" class="form-label">Aktuelles Passwort</label>
                        <input type="password" name="current_password" id="current_password" class="form-control @error('current_password') is-invalid @enderror">
                        @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Neues Passwort</label>
                        <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror">
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Neues Passwort bestätigen</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
                    </div>

                    <button type="submit" class="btn btn-primary settings-btn w-100">Ändern</button>
                </form>
            </div>
        </div>
    </div>
@endsection
