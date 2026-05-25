<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureGuard
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (! $user || ! $user->isGuard()) {
            abort(403, 'Security guard access required.');
        }

        if (! $user->society_id || $user->society?->isBlocked()) {
            auth()->logout();

            return redirect()->route('login')
                ->with('error', 'Your society account is inactive.');
        }

        return $next($request);
    }
}
