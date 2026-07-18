# 🌌 19_DEPLOYMENT.md

## 🎯 Tujuan (Goal)
Dokumen ini menyusun panduan rilis produksi (Deployment Guide) CosmicLib Library pada Shared Hosting cPanel, mencakup konfigurasi berkas `.env`, pemetaan folder `public_html`, dan pengaturan Cron Job.

---

## 🗂️ Table of Contents
1. [Arsitektur Folder cPanel (Symlink vs Move)](#arsitektur-folder-cpanel-symlink-vs-move)
2. [Langkah Deployment Manual via FTP/File Manager](#langkah-deployment-manual-via-ftpfile-manager)
3. [Konfigurasi Cron Job Penjadwal Laravel](#konfigurasi-cron-job-penjadwal-laravel)
4. [Langkah Optimasi Produksi](#langkah-optimasi-produksi)

---

## ⚙️ Placeholder & Kerangka Sistem

### Arsitektur Folder cPanel (Symlink vs Move)
*Menjabarkan metode terbaik mengamankan folder sistem (diletakkan di luar `public_html`) dan hanya mengunggah isi folder `public` ke dalam `public_html`.*

### Langkah Deployment Manual via FTP/File Manager
*Placeholder: Panduan mengompresi proyek menjadi zip (mengecualikan `node_modules` dan `vendor`), mengunggahnya ke cPanel File Manager, dan mengekstrak berkas.*

### Konfigurasi Cron Job Penjadwal Laravel
*Placeholder: Penulisan perintah cron cPanel seperti `* * * * * php /home/username/cosmiclib/artisan schedule:run >> /dev/null 2>&1` untuk menjalankan tugas rutin.*

### Langkah Optimasi Produksi
*Placeholder: Menjalankan `composer install --optimize-autoloader --no-dev`, `php artisan config:cache`, `php artisan route:cache`, dan `php artisan view:cache`.*
