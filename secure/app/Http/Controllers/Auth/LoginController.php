<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\CookieConsent;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (auth()->check()) {
            return redirect()->route('home');
        }
        return view('auth.login', [
            'title' => 'Anmelden',
            'header' => false,
            'footer' => false,
        ]);
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();
        $remember = $request->has('remember');
        if (!CookieConsent::accepted()) {
            $remember = false;
        }

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            if (!$user->is_active) {
                Auth::logout();
                return back()->withErrors(['email' => 'Konto ist nicht aktiv.']);
            }

            if (!$user->email_verified_at) {
                Auth::logout();
                return back()->with(['error-verify' => 'E-Mail wurde noch nicht bestätigt.']);
            }

            $request->session()->regenerate();

            return redirect()->intended(route('home'));
        }

        return back()->withErrors(['email' => 'Ungültige Anmeldedaten.']);
    }
}
