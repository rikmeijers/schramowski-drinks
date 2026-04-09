@extends('shared.layout')

@section('content')
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
        <div>
            <h1 class="fw-bold mb-1">Benutzerverwaltung</h1>
            <p class="text-body-secondary mb-0">Übersicht und Verwaltung der Benutzer.</p>
        </div>
        <a href="{{ route('register.form') }}" class="btn btn-primary rounded-pill px-4 ms-2">
            <i class="bi bi-person-plus me-2"></i> Neuer Benutzer
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-4 mt-3">
            {{ session('success') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger border-0 shadow-sm rounded-4 mt-3">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-3">Benutzer</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>E-Mail</th>
                            <th>Rolle</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->role }}</td>
                                <td>{{ $user->is_active ? 'Aktiv' : 'Inaktiv' }}</td>
                                <td>
                                    @if(!$user->isAdmin())
                                    <form method="POST" action="{{ route('users.destroy', $user->id) }}" onsubmit="return confirm('Möchtest du diesen Benutzer wirklich löschen?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger rounded-pill" type="submit">
                                            <i class="bi bi-trash"></i> Löschen
                                        </button>
                                    </form>
                                    @else
                                        <span class="text-muted small">Admin</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center">Keine Benutzer gefunden.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

