<?php

namespace App\Http\Middleware;

use App\Models\AkunAdmin;
use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (! session('auth_is_admin')) {
            return redirect()->route('login');
        }

        $adminId = session('auth_admin_id') ?? session('auth_guru_id');
        $admin = $adminId ? AkunAdmin::find($adminId) : null;

        if (! $admin || (isset($admin->is_aktif) && $admin->is_aktif == 0)) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login');
        }

        return $next($request);
    }
}
