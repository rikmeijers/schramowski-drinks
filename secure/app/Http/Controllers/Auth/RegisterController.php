<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Mail\WelcomeMail;
use App\Models\User;
use App\Models\UserToken;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class RegisterController extends Controller
{
    public function showRegisterForm()
    {
        if (!auth()->user() || !auth()->user()->can('admin')) {
            abort(403);
        }
        return view('auth.register', [
            'title' => 'Benutzer registrieren',
            'header' => false,
            'footer' => false,
        ]);
    }

    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'is_active' => true,
            'role' => 'USER',
        ]);

        $token = Str::random(64);
        UserToken::create([
            'user_id' => $user->id,
            'token' => $token,
            'type' => 'EMAIL_VERIFICATION',
            'expires_at' => now()->addMinutes(15),
        ]);

        $action_url = url('/verify-email/' . $token);
        Mail::to($user->email)->send(new WelcomeMail($user->email, $action_url));

        // Admin erstellt einen Benutzer; danach zurück zur Benutzerverwaltung.
        return redirect()->route('users.index')
            ->with(['success' => 'Benutzer erstellt! Bitte Posteingang prüfen, um die E-Mail zu bestätigen.']);
    }
}
