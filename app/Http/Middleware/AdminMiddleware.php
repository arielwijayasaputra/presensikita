<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!session('auth_guru_id')) {
            return redirect()->route('login');
        }

        if (!session('auth_is_admin')) {
            return redirect()->route('guru.index');
        }

        return $next($request);
    }
}
