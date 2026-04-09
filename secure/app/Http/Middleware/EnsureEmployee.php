<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmployee
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(401);
        }

        // Aanname: iedereen die kan inloggen (= actief + geverifieerd) is medewerker.
        // Als je later echte rollen wilt, kun je hier bijvoorbeeld ADMIN/EMPLOYEE afdwingen.
        if (!$user->isActive() || !$user->email_verified_at) {
            abort(403);
        }

        return $next($request);
    }
}

