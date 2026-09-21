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

**PresensiKita** adalah platform presensi dan jurnal mengajar digital berbasis web untuk **SMKN 1 Boyolangu**, menggantikan proses manual dengan pencatatan kehadiran *real-time*, alur izin digital, notifikasi WhatsApp otomatis, dan rekapitulasi multi-level.

---

## 🚀 Fitur Utama

| Fitur | Penjelasan |
|---|---|
| 📋 **Absensi & Jurnal KBM** | Guru mengisi kehadiran siswa (Hadir, Sakit, Izin, Alpa) dan materi KBM sesuai jam mengajar aktif. Mendukung mode "Hadir Semua" dengan pencarian nama untuk koreksi individual. |
| ⏰ **Deteksi Jam Aktif Otomatis** | Sistem mencocokkan jadwal guru dengan jam pelajaran hari ini secara real-time; guru hanya dapat mengisi jurnal pada jam mengajar yang sedang berlangsung. |
| 📢 **Notifikasi WhatsApp** | Bot WhatsApp otomatis mengirim notifikasi izin guru ke Kepsek, Waka Kesiswaan, dan Waka SDM melalui gateway WhatsApp yang terintegrasi. Notifikasi dalam aplikasi otomatis hilang setelah 1×24 jam. |
| ⚡ **Izin Guru (Signed URL)** | Guru mengajukan izin mengajar online dengan bukti foto/surat; persetujuan Waka/Kepsek cukup 1-klik via link aman tanpa perlu login. |
| 🛡️ **Dispensasi Siswa** | Guru Piket menerbitkan surat dispensasi siswa; persetujuan digital dikirim ke wali siswa. Satpam mencatat jam keluar dan masuk siswa secara presisi. |
| 🕐 **Keterlambatan Siswa** | Guru Piket mencatat siswa terlambat beserta alasan; riwayat tersimpan dan dapat ditinjau. |
| 📊 **Rekap & Export** | Wali Kelas mengunduh rekap absensi & jurnal per kelas (CSV/Excel). Waka SDM mengunduh rekap kehadiran seluruh guru. |
| 📂 **Data Master** | CRUD lengkap: Guru, Siswa, Kelas, Jurusan, Mata Pelajaran, Jadwal Mengajar. Import massal via file Excel/CSV. |
| 🗓️ **Jadwal Mengajar** | Import jadwal dari CSV, tugaskan guru per slot, edit manual, atau hapus semua dan unggah ulang. Mendukung alih jadwal dan pengosongan jadwal guru. |
| 🔔 **Jam Pelajaran Fleksibel** | Admin mengatur waktu mulai/selesai tiap jam pelajaran (Senin–Kamis dan Jumat terpisah), termasuk jam istirahat, berlaku ke semua hari sekaligus. |
| 🎓 **Kenaikan Kelas & Alumni** | Wizard kenaikan kelas dengan preview sebelum dieksekusi; siswa kelas XII otomatis dipindahkan ke daftar alumni. |
| 👪 **Portal Orang Tua** | Orang tua memantau riwayat kehadiran anak cukup dengan memasukkan NISN, tanpa perlu akun. |
| 📣 **Laporan Pengaduan Publik** | Masyarakat umum dapat mengirim laporan/aduan ke sekolah tanpa login; Admin mengelola status setiap laporan. |
| ⚙️ **Pengaturan Sekolah** | Admin mengatur nama sekolah, NPSN, kontak, batas waktu pengisian jurnal, izin edit jurnal, serta konfigurasi WhatsApp Bot Gateway (endpoint, nomor bot, QR scan, status koneksi). |

---

## 👥 8 Role Pengguna

| Role | Guard / Akses | Halaman & Kemampuan Utama |
|---|:---:|---|
| **Admin** | `akun_admin` | Dashboard rekap sekolah · CRUD Guru, Siswa, Kelas, Jurusan, Mapel · Import Excel/CSV · Jadwal Mengajar · Jam Pelajaran · Guru Piket · Kenaikan Kelas · Alumni · Laporan Masuk · Pengaturan sekolah & WhatsApp Bot · Riwayat absensi |
| **Guru** | `guru` | Isi absensi & jurnal KBM jam aktif · Ajukan izin mengajar dengan bukti · Lihat siswa terlambat hari ini · Ubah profil |
| **Wali Kelas** | `guru` | Pantau absensi harian kelas binaan · Jurnal harian & rekap 1 tahun · Export rekap absensi & jurnal (CSV) · Simpan absensi harian |
| **Guru Piket** | `guru` | Terbitkan surat dispensasi siswa · Catat siswa terlambat · Input izin guru darurat · Absensi siswa harian |
| **Waka / Kepsek** | `guru` | Dashboard rekap sekolah · Setujui atau tolak izin guru via signed link · Pantau grafik kehadiran |
| **Waka SDM** | `guru` | Evaluasi kedisiplinan guru · Export rekap kehadiran pengajar (CSV) · Pengaturan WhatsApp Bot |
| **Satpam** | `akun_satpam` | Validasi gerbang: catat jam keluar & jam masuk siswa pemegang surat dispensasi · Riwayat dispensasi harian |
| **Orang Tua** | NISN *(tanpa akun)* | Cek riwayat kehadiran anak secara transparan langsung via NISN |

---

## 🏫 10 Jurusan SMKN 1 Boyolangu

> Akuntansi dan Keuangan Lembaga · Animasi · Bisnis Digital · Desain Komunikasi Visual · Manajemen Perkantoran · Produksi dan Siaran Program Televisi · Rekayasa Perangkat Lunak · Teknik Komputer dan Informatika · Teknik Komputer dan Jaringan · Usaha Layanan Wisata

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
| `php artisan test` | Menjalankan seluruh test suite (40 test, 257 assertions) |
| `vendor/bin/pint` | Memformat kode secara otomatis sesuai standar PSR-12 |

---

<p align="center">
  <sub>PresensiKita • <b>SMKN 1 Boyolangu</b> • Tahun Ajaran 2026/2027</sub>
</p>
