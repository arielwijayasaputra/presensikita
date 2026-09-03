<?php

use App\Models\Guru;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public function up(): void
    {
        $admin = Guru::where('is_admin', 1)->orderBy('id_guru')->first();
        if (! $admin) {
            $admin = Guru::firstOrNew(['username' => 'admin']);
            $admin->nama_guru = $admin->nama_guru ?: 'Administrator';
        }

        Guru::where('is_admin', 1)->where('id_guru', '<>', $admin->getKey())->update(['is_admin' => 0]);
        Guru::where('username', 'admin')->where('id_guru', '<>', $admin->getKey())->update([
            'username' => 'guru_admin_'.$admin->getKey(),
        ]);
        $admin->username = 'admin';
        $admin->password_hash = Hash::make('admin123');
        $admin->is_admin = 1;
        $admin->is_aktif = 1;
        $admin->Peran = 'Administrator';
        $admin->save();
    }

    public function down(): void
    {
        // Credentials are intentionally not restored during rollback.
    }
};
