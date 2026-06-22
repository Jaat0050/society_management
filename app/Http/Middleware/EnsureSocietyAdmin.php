<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSocietyAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (! $user || ! $user->isSocietyAdmin()) {
            abort(403, 'Society Admin access required.');
        }

        if (! $user->society_id || $user->society?->isBlocked()) {
            auth()->logout();

            return redirect()->route('login')
                ->with('error', 'Your society account is inactive or blocked.');
        }

        return $next($request);
    }
}
