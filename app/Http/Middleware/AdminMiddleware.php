<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Не авторизован или не админ — показать 404
        if (!$user || !$user->hasRole('admin')) {
            abort(404);
        }

        return $next($request);
    }
}
