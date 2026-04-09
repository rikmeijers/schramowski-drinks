<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Mail\ResetPassword;
use App\Models\User;
use App\Models\UserToken;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordController extends Controller
{
    public function showForgotForm()
    {
        return view('auth.forgot-password', [
            'title' => 'Passwort vergessen',
            'header' => false,
            'footer' => false
        ]);
    }

    public function sendResetLink(ForgotPasswordRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        $user->tokens()->where('type', 'PASSWORD_RESET')->delete();

        $token = Str::random(64);
        UserToken::create([
            'user_id' => $user->id,
            'token' => $token,
            'type' => 'PASSWORD_RESET',
            'expires_at' => now()->addMinutes(15),
        ]);

        $action_url = url('/reset-password/' . $token);
        Mail::to($user->email)->send(new ResetPassword($user->email, $action_url));

        return back()->with(['success' => 'Link zum Zurücksetzen des Passworts wurde gesendet.']);
    }

    public function showResetForm($token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'title' => 'Passwort zurücksetzen',
            'header' => false,
            'footer' => false
        ]);
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        $tokenRecord = UserToken::where('token', $request->token)
            ->where('type', 'PASSWORD_RESET')
            ->valid()
            ->first();

        if (!$tokenRecord) {
            return back()->withErrors(['token' => 'Ungültiges oder abgelaufenes Token.']);
        }

        $user = $tokenRecord->user;
        $user->password = $request->password;
        $user->save();

        $tokenRecord->delete();

        return redirect()->route('login.form')->with(['success' => 'Passwort erfolgreich geändert!']);
    }
}
