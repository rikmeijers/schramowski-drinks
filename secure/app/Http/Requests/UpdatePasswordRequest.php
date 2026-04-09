<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function messages(): array
    {
        return [
            'current_password.required' => 'Bitte gib dein aktuelles Passwort ein.',
            'current_password.string' => 'Das aktuelle Passwort muss eine Zeichenkette sein.',
            'password.required' => 'Bitte gib ein neues Passwort ein.',
            'password.string' => 'Das Passwort muss eine Zeichenkette sein.',
            'password.min' => 'Das Passwort muss mindestens 8 Zeichen lang sein.',
            'password.confirmed' => 'Die Passwörter stimmen nicht überein.',
        ];
    }
}
