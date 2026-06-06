@extends('shared.layout')

@section('content')
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
        <div>
            <h1 class="fw-bold mb-1">Benutzerverwaltung</h1>
            <p class="text-body-secondary mb-0">Übersicht und Verwaltung der Benutzer.</p>
        </div>
        <a href="{{ route('register.form') }}" class="btn btn-primary rounded px-4 ms-2">
            <i class="bi bi-person-plus me-2"></i> Neuer Benutzer
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <div class="card ui-card">
        <div class="card-body">
            <h5 class="fw-bold mb-3">Benutzer</h5>
            <div class="table-responsive">
                <table class="table ui-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>E-Mail</th>
                            <th>Rolle</th>
                            <th>Status</th>
                            <th class="text-end"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td class="fw-semibold">{{ $user->id }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @if($user->isAdmin())
                                        <span class="badge bg-primary">Admin</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($user->role) }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($user->is_active)
                                        <span class="badge bg-success">Aktiv</span>
                                    @else
                                        <span class="badge bg-secondary">Inaktiv</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if(!$user->isAdmin())
                                    <form method="POST" action="{{ route('users.destroy', $user->id) }}" onsubmit="return confirm('Möchtest du diesen Benutzer wirklich löschen?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger rounded px-3" type="submit">
                                            <i class="bi bi-trash me-1"></i>Löschen
                                        </button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-body-secondary py-5">Keine Benutzer gefunden.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

