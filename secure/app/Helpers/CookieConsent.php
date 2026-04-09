<?php

namespace App\Helpers;

class CookieConsent
{
    public static function accepted(): bool
    {
        $cookie = request()->cookie('laravel_cookie_consent');

        return $cookie === '1';
    }
}
