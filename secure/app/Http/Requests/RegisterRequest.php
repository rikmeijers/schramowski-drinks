<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Nur Admins dürfen Benutzer registrieren
        return auth()->user() && auth()->user()->can('admin');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Bitte gib einen Namen ein.',
            'name.string' => 'Der Name muss eine Zeichenkette sein.',
            'name.max' => 'Der Name darf nicht mehr als 255 Zeichen enthalten.',
            'email.required' => 'Bitte gib eine E-Mail-Adresse ein.',
            'email.string' => 'Die E-Mail-Adresse muss eine Zeichenkette sein.',
            'email.email' => 'Bitte gib eine gültige E-Mail-Adresse ein.',
            'email.max' => 'Die E-Mail-Adresse darf nicht mehr als 255 Zeichen enthalten.',
            'email.unique' => 'Diese E-Mail-Adresse wird bereits verwendet.',
            'password.required' => 'Bitte gib ein Passwort ein.',
            'password.string' => 'Das Passwort muss eine Zeichenkette sein.',
            'password.min' => 'Das Passwort muss mindestens 8 Zeichen lang sein.',
            'password.confirmed' => 'Die Passwörter stimmen nicht überein.',
        ];
    }
}
