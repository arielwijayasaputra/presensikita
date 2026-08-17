<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class StrukturalMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!session('auth_guru_id')) {
            return redirect()->route('login');
        }

        $role = session('auth_role', '');
        $allowed = ['waka', 'kepsek', 'satpam', 'guru_piket', 'walikelas'];

        if (!in_array($role, $allowed)) {
            if (session('auth_is_admin')) {
                return redirect()->route('admin.index');
            }
            return redirect()->route('guru.index');
        }

        return $next($request);
    }
}
