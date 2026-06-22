<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();

                if ($user->isSuperAdmin()) {
                    return redirect()->route('super-admin.dashboard');
                }

                if ($user->isSocietyAdmin()) {
                    return redirect()->route('admin.dashboard');
                }

                if ($user->isGuard()) {
                    return redirect()->route('guard.dashboard');
                }

                if ($user->isResident()) {
                    return redirect()->route('resident.dashboard');
                }

                return redirect()->route('login');
            }
        }

        return $next($request);
    }
}
