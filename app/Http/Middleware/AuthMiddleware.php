<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!session('auth_guru_id')) {
            return redirect()->route('login');
        }
        return $next($request);
    }
}
