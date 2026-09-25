# SIPPM &mdash; PG Rendeng

Sistem Informasi Pelaporan & Penanganan Kerusakan Mesin Giling, dibangun dengan
**Laravel 10** (PHP) mengikuti tampilan & alur pada mockup
`mockup-sippm-pgrendeng-v12-light-corporate.html`.

Aplikasi ini murni server-rendered (Blade + sedikit JavaScript untuk
interaksi seperti dropdown Stasiun → Mesin → Kondisi, toggle sidebar mobile,
dan pratinjau unggah foto) — **tidak perlu Node.js/npm** untuk menjalankannya.

## 1. Persyaratan

- XAMPP (Apache + MySQL) dengan **PHP 8.1 atau lebih baru**
- [Composer](https://getcomposer.org/) (untuk mengunduh dependency Laravel)

## 2. Instalasi

1. Salin folder proyek ini ke `htdocs` XAMPP, misalnya:
   `C:\xampp\htdocs\sippm` atau `/Applications/XAMPP/htdocs/sippm`.
2. Buka terminal di folder proyek, lalu jalankan:
   ```bash
   composer install
   ```
3. Salin file environment:
   ```bash
   cp .env.example .env
   # Windows (Command Prompt): copy .env.example .env
   ```
4. Buat key aplikasi:
   ```bash
   php artisan key:generate
   ```
5. Buat database MySQL kosong bernama **`sippm_pgrendeng`** lewat phpMyAdmin
   (Start Apache & MySQL di XAMPP Control Panel terlebih dahulu), atau lewat
   terminal:
   ```bash
   mysql -u root -e "CREATE DATABASE sippm_pgrendeng;"
   ```
   Sesuaikan `DB_USERNAME` / `DB_PASSWORD` di file `.env` bila konfigurasi
   MySQL XAMPP Anda berbeda dari default (`root` tanpa password).
6. Jalankan migrasi database beserta data contoh (akun demo & laporan
   contoh):
   ```bash
   php artisan migrate --seed
   ```
7. Buat symbolic link penyimpanan publik (untuk foto hasil penanganan):
   ```bash
   php artisan storage:link
   ```
8. Jalankan server:
   ```bash
   php artisan serve
   ```
   Buka **http://127.0.0.1:8000** di browser.

   Alternatif: gunakan Apache bawaan XAMPP dengan mengarahkan *Document
   Root* / Virtual Host ke folder `public/` pada proyek ini, lalu akses
   melalui `http://localhost/` (bukan `http://localhost/sippm/public`,
   supaya path asset & route berjalan normal).

## 3. Akun Demo

Kata sandi seluruh akun demo di bawah adalah **`password123`**.

| Peran    | Username         | Nama            |
|----------|------------------|-----------------|
| Manager  | `sri.manager`    | Sri Handayani   |
| Operator | `andi.operator`  | Andi Wijaya     |
| Operator | `slamet.operator`| Slamet Riyadi   |
| Teknisi  | `budi.teknisi`   | Budi Santoso    |
| Teknisi  | `rahmat.teknisi` | Rahmat Hidayat  |

Akun `eko.operator` dan `dedi.teknisi` sengaja diseed dalam status
**nonaktif** untuk menguji alur aktif/nonaktif akun.

## 4. Alur Sistem (Ringkas)

1. **Operator** membuat laporan kerusakan (`Buat Laporan`) → status
   `Menunggu Validasi`.
2. **Manager** memvalidasi laporan (`Validasi Laporan`): **Terima** →
   lanjut ke `Penugasan Teknisi`, atau **Tolak** (wajib isi alasan) →
   laporan berstatus `Ditolak` dan operator dapat melihat alasannya.
3. **Manager** menugaskan teknisi, prioritas, & waktu mulai pengerjaan →
   status `Ditugaskan`.
4. **Teknisi** membuka tugas, menekan **Mulai Pemeriksaan** → status
   `Dalam Penanganan`, lalu mengisi **Form Hasil Penanganan** (hasil
   pemeriksaan, penyebab, tindakan, komponen, waktu selesai, foto) →
   dikirim ke Manager, status `Menunggu Validasi Akhir`. Downtime
   dihitung otomatis dari selisih waktu mulai & selesai.
5. **Manager** melakukan **Validasi Akhir**: **Setujui** → laporan
   `Selesai` dan masuk ke Histori, atau **Kembalikan ke Teknisi** (wajib
   isi alasan) → status kembali ke `Ditugaskan` untuk diperbaiki.
6. **Manager** dapat mengelola akun Operator & Teknisi (buat akun baru
   dengan username & kata sandi sementara otomatis, reset kata sandi,
   aktifkan/nonaktifkan) melalui menu **Kelola Akun**.
7. Akun baru / akun yang baru direset kata sandinya **wajib mengganti
   kata sandi** pada saat login pertama sebelum dapat mengakses menu
   lain (dialihkan otomatis ke halaman Profil Saya).

## 5. Struktur Data Referensi

Daftar Stasiun → Mesin, Kategori → Kondisi/Abnormalitas, serta pilihan
Area/Bagian pada form "Tambah Akun" ada di `config/sippm.php`, diporting
langsung dari data JavaScript pada mockup agar identik.

## 6. Struktur Folder Penting

```
app/Http/Controllers/Operator   Dashboard & Buat/Detail Laporan
app/Http/Controllers/Manager    Dashboard, Validasi, Penugasan, Validasi Akhir, Histori, Kelola Akun
app/Http/Controllers/Teknisi    Dashboard, Detail Tugas, Form Hasil, Riwayat
app/Models/User.php             Akun (operator/manager/teknisi)
app/Models/Laporan.php          Laporan kerusakan & alur status
resources/views/                Tampilan (Blade), identik dengan mockup
public/css/app.css              CSS asli dari mockup (tanpa diubah)
public/js/app.js                Interaksi UI (dropdown custom, unggah foto)
database/seeders/               Akun demo & contoh laporan
```
