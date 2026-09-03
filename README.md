<p align="center">
  <img src="banner.svg" alt="PresensiKita Banner" width="100%">
</p>

<p align="center">
  <a href="https://laravel.com"><img src="https://img.shields.io/badge/Laravel-13.x-0284c7?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 13"></a>
  <a href="https://php.net"><img src="https://img.shields.io/badge/PHP-%5E8.3-0369a1?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.3+"></a>
  <a href="https://mysql.com"><img src="https://img.shields.io/badge/MySQL-8.0-075985?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL 8.0"></a>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/SMKN_1_Boyolangu-Official-0284c7?style=for-the-badge&logo=googleclassroom&logoColor=white" alt="SMKN 1 Boyolangu">
  <img src="https://img.shields.io/badge/Multi--Role-8_Roles-0ea5e9?style=for-the-badge&logo=auth0&logoColor=white" alt="8 Roles">
</p>

<p align="center">
  <a href="#-sekilas-sistem"><b>Sekilas</b></a> •
  <a href="#-fitur-utama"><b>Fitur Utama</b></a> •
  <a href="#-8-role-pengguna"><b>8 Role</b></a> •
  <a href="#-instalasi-cepat"><b>Instalasi</b></a> •
  <a href="#-perintah-developer"><b>Perintah</b></a>
</p>

---

## ⚡ Sekilas Sistem

**PresensiKita** adalah platform presensi dan jurnal mengajar digital berbasis web untuk **SMKN 1 Boyolangu**, menggantikan jurnal kertas manual dengan pencatatan kehadiran *real-time*, alur izin digital, dan rekapitulasi otomatis.

---

## 🚀 Fitur Utama

| Fitur | Penjelasan Singkat |
|---|---|
| 📋 **Presensi & Jurnal Kelas** | Guru mengisi kehadiran siswa (Hadir, Sakit, Izin, Alpa) dan materi KBM sesuai jam mengajar aktif. |
| ⚡ **Izin Guru (Signed URL)** | Pengajuan izin online dengan bukti foto/surat; persetujuan pimpinan cukup 1-klik via link aman tanpa login. |
| 🛡️ **Dispensasi Siswa & Satpam** | Siswa izin keluar sekolah tervalidasi di pos gerbang; Satpam mencatat jam presisi keluar & masuk. |
| 📊 **Rekapitulasi & Export CSV** | Hitung persentase kehadiran per kelas/guru otomatis serta download laporan rekap format CSV/Excel. |
| 📂 **Data Master & Kenaikan Kelas** | Import data massal via Excel, penataan jadwal fleksibel, serta wizard kenaikan kelas & alumni otomatis. |
| 👨‍👩‍👧 **Portal Ortu & Pengaduan** | Orang tua memantau kehadiran anak via NISN; masyarakat umum dapat mengirim laporan aduan tanpa login. |

---

## 👥 8 Role Pengguna

| Role | Login Guard | Wewenang & Tanggung Jawab |
|---|:---:|---|
| **Admin** | `akun_admin` | Kelola master data (siswa, guru, kelas, mapel, jadwal), kenaikan kelas, dan aduan publik |
| **Guru** | `guru` | Isi absensi & jurnal KBM pada jam aktif hari ini, ajukan permohonan izin mengajar |
| **Wali Kelas** | `guru` | Pantau kehadiran siswa kelas binaan dan unduh rekap absensi / jurnal (CSV) |
| **Guru Piket** | `guru` | Pantau ketertiban harian sekolah, terbitkan surat dispen siswa & input izin darurat |
| **Waka & Kepsek** | `guru` | Pantau rekapitulasi sekolah, grafik tren kehadiran, dan persetujuan izin resmi |
| **Waka SDM** | `guru` | Evaluasi kedisiplinan guru dan download rekap kehadiran pengajar (CSV) |
| **Satpam** | `akun_satpam` | Validasi gerbang: catat jam keluar dan jam masuk siswa pemegang surat dispensasi |
| **Orang Tua** | `siswa` *(NISN)* | Cek riwayat kehadiran anak secara transparan tanpa perlu kata sandi |

---

## 💻 Instalasi Cepat

```bash
# 1. Clone repository
git clone git@github.com:arielwijayasaputra/presensikita.git
cd presensikita

# 2. Setup otomatis (.env, migrate, seed, build)
composer run setup

# 3. Jalankan server
composer run dev
```

> Buka di browser: **`http://127.0.0.1:8000`**

<details>
<summary><b>🛠️ Klik di sini untuk panduan instalasi manual</b></summary>

<br>

```bash
composer install
cp .env.example .env
php artisan key:generate

# Konfigurasi DB di file .env, kemudian:
php artisan migrate --force
php artisan db:seed
php artisan db:seed --class=GuruExcelSeeder
php artisan storage:link

npm install --ignore-scripts
npm run build
composer run dev
```
</details>

---

## 🛠️ Perintah Developer

| Perintah | Deskripsi Fungsi |
|---|---|
| `composer run dev` | Menjalankan Server Laravel, Queue Worker, dan Vite sekaligus |
| `php artisan migrate:fresh --seed` | Reset database dan seeding data awal |
| `php artisan db:seed --class=GuruExcelSeeder` | Import ulang data guru dari file Excel |
| `php artisan optimize:clear` | Membersihkan semua cache konfigurasi, route, dan view |
| `vendor/bin/pint` | Memformat kode secara otomatis sesuai standar PSR-12 |

---

<p align="center">
  <sub>PresensiKita • <b>SMKN 1 Boyolangu</b></sub>
</p>
