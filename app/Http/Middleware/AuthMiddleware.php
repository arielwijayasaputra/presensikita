<?php

namespace App\Http\Middleware;

use App\Models\Guru;
use Closure;
use Illuminate\Http\Request;

class AuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (! session('auth_guru_id')) {
            return redirect()->route('login');
        }

        $guru = Guru::find(session('auth_guru_id'));
        if (! $guru || $guru->is_aktif == 0) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login');
        }

        return $next($request);
    }
}
