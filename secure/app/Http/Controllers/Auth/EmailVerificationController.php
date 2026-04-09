<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\VerifyEmail;
use App\Models\UserToken;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\ResendVerificationRequest;

class EmailVerificationController extends Controller
{
    public function verify($token)
    {
        $tokenRecord = UserToken::where('token', $token)
            ->where('type', 'EMAIL_VERIFICATION')
            ->valid()
            ->first();

        if (!$tokenRecord) {
            return redirect()->route('login.form')->with(['error-verify' => 'Ungültiges oder abgelaufenes Token.']);
        }

        $user = $tokenRecord->user;
        $user->email_verified_at = now();
        $user->save();

        $tokenRecord->delete();

        session()->put('success', 'E-Mail erfolgreich bestätigt!');
        return redirect()->route('login.form');
    }

    public function showResendForm()
    {
        return view('auth.resend-verification', [
            'title' => 'Bestätigungs-E-Mail erneut senden',
            'header' => false,
            'footer' => false,
        ]);
    }

    public function resend(ResendVerificationRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user->email_verified_at) {
            $user->tokens()->where('type', 'EMAIL_VERIFICATION')->delete();

            $token = Str::random(64);
            UserToken::create([
                'user_id' => $user->id,
                'token' => $token,
                'type' => 'EMAIL_VERIFICATION',
                'expires_at' => now()->addMinutes(15),
            ]);

            $action_url = url('/verify-email/' . $token);
            Mail::to($user->email)->send(new VerifyEmail($user->email, $action_url));
        }

        return back()->with(['success' => 'Bestätigungs-E-Mail wurde gesendet.']);
    }
}
