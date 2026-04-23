# Sistem Informasi Pengajuan ATK (Alat Tulis Kantor)

Aplikasi web berbasis **Laravel 12** yang dikembangkan untuk mengelola, melacak, dan mendokumentasikan proses pengajuan dan peminjaman Alat Tulis Kantor (ATK) secara sistematis. Aplikasi ini memudahkan alur kerja antara pihak pengaju (karyawan/staf) hingga ke pihak admin untuk proses persetujuan dan rekapitulasi data.

## 🚀 Teknologi yang Digunakan

Aplikasi ini dibangun menggunakan arsitektur modern (TALL stack / kombinasi Vue/Blade) dengan rincian teknologi sebagai berikut:

### Backend
* **Bahasa Pemrograman:** PHP (Mendukung PHP versi `^8.2`)
* **Framework:** Laravel `^12.0` (Versi terbaru)
* **Database:** MySQL / MariaDB (Dijalankan via XAMPP)
* **Routing & Controllers:** Laravel native HTTP Controller

### Frontend
* **Templating Engine:** Blade UI (Ekstensi `.blade.php`)
* **CSS Framework:** TailwindCSS `^4.0`
* **Bundler / Build Tool:** Vite `^7.0` (beserta `laravel-vite-plugin`)
* **HTTP Client Asinkron**: Axios `^1.11` (Untuk pemrosesan *request* REST API/AJAX secara dinamis)
* **Manajemen Aset (Asset Pipeline):** Node.js & NPM

## 📌 Fitur Utama

Berdasarkan struktur sistem yang telah dibangun, aplikasi ini meliputi fungsionalitas berikut:
1. **Otentikasi & Keamanan (Auth)**: Sistem Login, Register, Middleware Admin, beserta manajemen sesi (Session).
2. **Pengajuan Barang/ATK**: Fungsionalitas CRUD (*Create, Read, Update, Delete*) untuk staf mengajukan ATK.
3. **Peminjaman ATK**: Sistem pendataan peminjaman barang.
4. **Laporan & Rekapitulasi**: Pembuatan (Generate) Laporan pengajuan barang ke dalam bentuk/format PDF menggunakan plugin PDF renderer.
5. **Notifikasi**: Sistem pemberitahuan status pengajuan/update terbaru.
6. **Manajemen Konten / Artikel**: Dashboard untuk mengelola artikel sistem maupun petunjuk/informasi umum.
7. **Pengaturan Sistem**: Konfigurasi umum aplikasi yang dikendalikan oleh Admin.

## 🛠 Panduan Instalasi (Development)

Untuk mengunduh dan menjalankan aplikasi ini secara lokal/development, ikuti langkah-langkah di bawah ini:

### Persiapan Prasyarat
- Telah ter-install **PHP 8.2** atau lebih baru.
- Telah ter-install **Composer**.
- Telah ter-install **Node.js** & **NPM**.
- Telah menyalakan layanan web server & database (**XAMPP** -> Apache & MySQL).

### Langkah Menjalankan Aplikasi
1. **Kloning Repositori**
   ```bash
   git clone https://github.com/Juawir/pengajuan-atk.git
   cd pengajuan-atk
   ```

2. **Instalasi Pustaka (Dependencies)**
   ```bash
   composer install
   npm install
   ```

3. **Konfigurasi Environment Database**
   Konfigurasi otomatis atau manual, ubah file `.env.example` menjadi `.env`.
   ```bash
   cp .env.example .env
   ```
   Pastikan mengubah konfigurasi database Anda di dalam `.env`:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=pengajuan_atk
   DB_USERNAME=root
   DB_PASSWORD=
   ```

4. **Generate Application Key & Migrasi Database**
   ```bash
   php artisan key:generate
   php artisan migrate
   ```

5. **Membuat Akun Pertama (Admin)**
   Karena sistem registrasi hanya dapat diakses oleh Admin, Anda perlu membuat akun pertama secara manual setelah instalasi. Ada dua cara untuk melakukannya:

   **Cara A: Menggunakan Seeder (Beserta Data Dummy)**
   Cocok untuk keperluan testing/development. Men-generate admin + beberapa user & data dummy.
   ```bash
   php artisan db:seed
   ```
   *(Login Admin Default: `admin@atk.com` | Password: `12345678`)*

   **Cara B: Menggunakan Laravel Tinker (Sistem Kosong/Produksi)**
   Gunakan jika Anda hanya ingin membuat akun Admin tanpa data dummy.
   Jalankan perintah ini di terminal:
   ```bash
   php artisan tinker
   ```
   Kemudian jalankan kode ini di dalam shell Tinker:
   ```php
   App\Models\User::create(['name' => 'Super Admin', 'email' => 'admin@domain.com', 'password' => 'password123', 'role' => 'admin', 'departemen' => 'IT']);
   ```
   *Ketik `exit` untuk keluar dari Tinker setelah selesai.*

6. **Jalankan Aplikasi**
   Buka 2 Terminal / Command Prompt terpisah secara bersamaan.
   
   Terminal 1 (Menjalankan server Laravel):
   ```bash
   php artisan serve
   ```
   
   Terminal 2 (Menjalankan server aset Vite TailwindCSS):
   ```bash
   npm run dev
   ```

6. **Selesai**
   Buka web browser dan akses melalui **[http://127.0.0.1:8000](http://127.0.0.1:8000)**.
   
---
*Dokumentasi ini di-generate secara otomatis untuk repositori pengajuan-atk.*
