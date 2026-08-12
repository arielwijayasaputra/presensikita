<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class OrangTuaMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!session('auth_siswa_id')) {
            return redirect()->route('login.orangtua');
        }
        return $next($request);
    }
}
