# ðŸŒŒ 34 — cPanel Deployment

## Deskripsi

Dokumen ini menyusun panduan deployment khusus untuk lingkungan **Shared Hosting cPanel**, yang merupakan target hosting utama CosmicLib Library, mencakup konfigurasi folder, FTP, Cron Job, dan optimasi khusus shared hosting.

## Tujuan

Memberikan panduan step-by-step yang detail dan mudah diikuti agar pustakawan sekolah atau administrator IT dapat men-deploy CosmicLib Library ke shared hosting cPanel tanpa memerlukan pengalaman DevOps.

## Ruang Lingkup

- Arsitektur folder cPanel (public_html vs project root)
- Deployment manual via FTP atau File Manager
- Konfigurasi Cron Job untuk Laravel scheduler
- Konfigurasi PHP version dan ekstensi
- Optimasi performa untuk resource terbatas
- Troubleshooting masalah umum cPanel

---

## 🗂️ Table of Contents

1. [Arsitektur Folder cPanel](#arsitektur-folder-cpanel)
2. [Langkah Deployment via File Manager](#langkah-deployment-via-file-manager)
3. [Konfigurasi PHP & Ekstensi](#konfigurasi-php--ekstensi)
4. [Konfigurasi Cron Job](#konfigurasi-cron-job)
5. [Konfigurasi Database MySQL](#konfigurasi-database-mysql)
6. [Optimasi Shared Hosting](#optimasi-shared-hosting)
7. [Troubleshooting Umum](#troubleshooting-umum)

---

## Status

`🟡 Blueprint` — Dokumen dalam tahap perancangan.

---

## ⚙️ Kerangka Sistem

### Arsitektur Folder cPanel

*Placeholder: Metode aman mengamankan folder sistem di luar `public_html`:*
```
/home/username/
├── cosmiclib/              ← Project root (di luar public_html)
│   ├── app/
│   ├── config/
│   ├── storage/
│   └── ...
└── public_html/            ← Document root (isi folder public/)
    ├── index.php           ← Entry point (path disesuaikan)
    ├── .htaccess
    └── assets/
```

### Langkah Deployment via File Manager

*Placeholder:*
1. Kompres project (tanpa `node_modules/`, `vendor/`, `.git/`)
2. Upload ZIP ke cPanel File Manager
3. Ekstrak ke `/home/username/cosmiclib/`
4. Salin isi `public/` ke `public_html/`
5. Edit `index.php` untuk menyesuaikan path
6. Upload `vendor/` yang sudah di-install secara lokal

### Konfigurasi PHP & Ekstensi

*Placeholder: Menggunakan MultiPHP Manager cPanel untuk memilih PHP 8.2+ dan mengaktifkan ekstensi yang diperlukan (PDO, mbstring, openssl, dll).*

### Konfigurasi Cron Job

*Placeholder:*
```
* * * * * /usr/local/bin/php /home/username/cosmiclib/artisan schedule:run >> /dev/null 2>&1
```

### Konfigurasi Database MySQL

*Placeholder: Membuat database dan user baru melalui cPanel MySQL Databases wizard. Menyesuaikan konfigurasi di `.env` file.*

### Optimasi Shared Hosting

*Placeholder:*
- Gunakan file cache driver (bukan Redis) karena shared hosting jarang memiliki Redis
- Minimalkan jumlah query dengan eager loading
- Kompres gambar secara agresif
- Hindari penggunaan queue worker — gunakan sync driver atau database driver

### Troubleshooting Umum

*Placeholder: Solusi untuk masalah umum:*
- Error 500: Periksa permission folder `storage/` dan `bootstrap/cache/`
- Blank page: Pastikan `APP_DEBUG=true` untuk melihat error (matikan setelah selesai)
- CSRF mismatch: Periksa konfigurasi `SESSION_DOMAIN` di `.env`
- Symlink error: Gunakan script PHP custom untuk membuat symlink

---

## Referensi

- [27_DEPLOYMENT.md](27_DEPLOYMENT.md)
- [17_INSTALLER_ENGINE.md](17_INSTALLER_ENGINE.md)
- [04_TECH_STACK.md](04_TECH_STACK.md)
- [22_SECURITY_GUIDELINE.md](22_SECURITY_GUIDELINE.md)

## Catatan

- Shared hosting cPanel adalah target utama — semua fitur harus berfungsi di environment ini.
- Hindari fitur yang memerlukan akses root, Redis, Supervisor, atau WebSocket.
- Sediakan alternatif manual untuk setiap proses yang biasanya menggunakan CLI.
