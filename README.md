```
+------------------------------------------------------------------------------------------------------------+
| ######   ######   #######   ######  #######  ##   ##   ######  #######  ##   ##  #######  #######   #####  |
| ##   ##  ##   ##  ##       ##       ##       ###  ##  ##          ##    ##  ##      ##       ##    ##   ## |
| ######   ######   #####    #######  #####    ## # ##  #######     ##    #####       ##       ##    ####### |
| ##       ##  ##   ##            ##  ##       ##  ###       ##     ##    ##  ##      ##       ##    ##   ## |
| ##       ##   ##  #######  ######   #######  ##   ##  ######   #######  ##   ##  #######     ##    ##   ## |
+------------------------------------------------------------------------------------------------------------+
```

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

**Fitur besar?** Kerja di branch terpisah:

```bash
git checkout -b nama-fitur
git push -u origin nama-fitur
```

## Cara Join Contributor (Windows)

> Prasyarat: [Laragon](https://laragon.org) sudah terinstall & running (Start All), dan sudah di-invite sebagai collaborator di GitHub (minta ke owner repo).

```cmd
:: 1. Setup SSH
cd D:\laragon\www
ssh-keygen -t ed25519 -C "email_kamu@example.com"
```

```powershell
:: 2. Aktifkan ssh-agent (PowerShell as Administrator, sekali aja)
Set-Service ssh-agent -StartupType Automatic
Start-Service ssh-agent
```

```cmd
:: 3. Load key & ambil public key
ssh-add $env:USERPROFILE\.ssh\id_ed25519
type $env:USERPROFILE\.ssh\id_ed25519.pub
```

Paste hasil copy ke **GitHub → Settings → SSH and GPG keys → New SSH key**, lalu accept undangan collaborator dari email/notifikasi GitHub.

```cmd
:: 4. Test koneksi & clone
ssh -T git@github.com
cd D:\laragon\www
git clone git@github.com:arielwijayasaputra/presensikita.git
cd presensikita

:: 5. Setup identitas git
git config --global user.name "Nama Kamu"
git config --global user.email "email kamu"

:: 6. Setup project
copy .env.example .env
composer install
php artisan key:generate
php artisan migrate
```

Sebelum `migrate`, buat database `presensikita` dulu di Laragon/HeidiSQL, lalu pastikan di `.env`:

```
DB_CONNECTION=mysql
DB_DATABASE=presensikita
DB_USERNAME=root
DB_PASSWORD=
```

```cmd
:: 7. Jalankan project
php artisan serve
```

mantabbb
