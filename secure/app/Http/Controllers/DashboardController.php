<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Redirect;

class DashboardController extends Controller
{
    public function users()
    {
        $users = User::orderBy('name')->get();
        return view('users.index', [
            'title' => 'Benutzer',
            'users' => $users,
        ]);
    }

    public function destroyUser($id)
    {
        $user = User::findOrFail($id);
        if ($user->isAdmin()) {
            return Redirect::back()->withErrors(['user' => 'Admin kann nicht gelöscht werden.']);
        }
        $user->delete();
        return Redirect::route('users.index')->with('success', 'Benutzer gelöscht.');
    }

    public function showRegisterForm()
    {
        return view('auth.register', [
            'title' => 'Benutzer hinzufügen',
            'header' => false,
            'footer' => false,
        ]);
    }
}
