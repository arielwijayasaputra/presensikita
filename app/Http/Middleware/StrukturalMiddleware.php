<?php

namespace App\Http\Middleware;

use App\Models\AkunSatpam;
use App\Models\Guru;
use App\Models\Role;
use Closure;
use Illuminate\Http\Request;

class StrukturalMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $role = session('auth_role', '');

        // Khusus role Satpam (menggunakan model AkunSatpam)
        if ($role === 'satpam' || session('auth_satpam_id')) {
            $satpamId = session('auth_satpam_id');
            $satpam = $satpamId ? AkunSatpam::find($satpamId) : null;
            if (! $satpam || $satpam->is_aktif == 0) {
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login');
            }

            return $next($request);
        }

        if (! session('auth_guru_id')) {
            return redirect()->route('login');
        }

        $guru = Guru::find(session('auth_guru_id'));
        if (! $guru || $guru->is_aktif == 0) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login');
        }

        $allowed = Role::getStrukturalSlugs();

        if (! in_array($role, $allowed)) {
            if (session('auth_is_admin')) {
                return redirect()->route('admin.index');
            }

            return redirect()->route('guru.index');
        }

        return $next($request);
    }
}
