**PresensiKita** adalah aplikasi web pengganti jurnal absensi guru manual, dibangun dengan Laravel. Guru mengisi absensi harian secara digital, sementara admin dapat memantau, mengelola, dan mengekspor rekap data absensi.

**Fitur Utama:**
- Pengisian absensi harian oleh guru (pengganti jurnal kertas)
- Manajemen data oleh admin (guru, kelas, jadwal, dll — sesuaikan)
- Laporan/rekap absensi otomatis
- Export laporan ke PDF & Excel
- Role yang tersedia: Admin dan Guru

**Tech Stack:**
- Laravel (PHP)
- MySQL/MariaDB

## Workflow

**Sebelum coding:**
```bash
git pull origin main
```

**Selesai coding:**
```bash
git add .
git commit -m "deskripsi singkat perubahan yang dilakukan"
git push origin main
```

**Aturan utama:**
- Jangan pernah commit `.env`
- Jangan push langsung ke `main` kalau kerja tim — pakai branch + Pull Request

**Fitur besar?** Kerja di branch terpisah:
```bash
git checkout -b nama-fitur
git push -u origin nama-fitur
```

**Mau gabung jadi contributor?** Minta di-invite dulu di GitHub, terus:
```bash
git clone git@github.com:arielwijayasaputra/presensikita.git
cd presensikita
composer install
```