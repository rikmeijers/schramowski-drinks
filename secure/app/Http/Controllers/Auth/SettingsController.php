<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Mail\VerifyEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Models\UserToken;
use Illuminate\Support\Str;

class SettingsController extends Controller
{
    public function showSettings()
    {
        return view('auth.settings', [
            'user' => auth()->user(),
            'title' => 'Einstellungen',
        ]);
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = auth()->user();
        $data = $request->validated();

        if ($data['email'] !== $user->email) {
            $user->email = $data['email'];
            $user->email_verified_at = null;
            $user->save();

            $user->tokens()->where('type', 'EMAIL_VERIFICATION')->delete();

            $token = Str::random(64);
            UserToken::create([
                'user_id' => $user->id,
                'token' => $token,
                'type' => 'EMAIL_VERIFICATION',
                'expires_at' => now()->addHours(24),
            ]);

            $action_url = url('/verify-email/' . $token);
            Mail::to($user->email)->send(new VerifyEmail($user->email, $action_url));

            $status = 'Profil gespeichert! Bitte bestätige deine neue E-Mail-Adresse.';
            Auth::logout();
            return redirect(route('login.form'))->with(['success' => $status]);
        } else {
            $user->name = $data['name'];
            $user->save();
            $status = 'Profil erfolgreich gespeichert.';
            return back()->with(['success' => $status]);
        }
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Das aktuelle Passwort ist falsch.']);
        }

        $user->password = $request->password;
        $user->save();

        return back()->with(['success' => 'Passwort erfolgreich geändert.']);
    }
}
