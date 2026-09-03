<?php

namespace App\Http\Middleware;

use App\Models\Siswa;
use Closure;
use Illuminate\Http\Request;

class OrangTuaMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (! session('auth_siswa_id') || session('auth_role') !== 'orangtua') {
            return redirect()->route('login');
        }

        $siswa = Siswa::find(session('auth_siswa_id'));
        if (! $siswa || (isset($siswa->is_aktif) && $siswa->is_aktif == 0)) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login');
        }

        return $next($request);
    }
}
