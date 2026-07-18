# 🌌 17 — Installer Engine

## Deskripsi

Dokumen ini merancang **Installer Engine** — skrip instalasi visual (Web Installer) CosmicLib yang memandu pengguna awam dalam mengonfigurasi koneksi MySQL, melakukan migrasi database, dan membuat akun admin pertama tanpa menggunakan terminal.

## Tujuan

Memungkinkan pustakawan sekolah atau administrator IT yang tidak memiliki pengalaman teknis untuk memasang CosmicLib Library melalui antarmuka web yang ramah pengguna, tanpa memerlukan akses SSH atau CLI.

## Ruang Lingkup

- Wizard instalasi multi-langkah berbasis web
- Pengecekan kebutuhan server otomatis (PHP, ekstensi, permission)
- Konfigurasi database visual dengan tombol tes koneksi
- Pembuatan akun administrator awal
- Penulisan otomatis file `.env`
- Penguncian installer setelah instalasi selesai

---

## 🗂️ Table of Contents

1. [Alur Langkah Penginstalan (Wizard Steps)](#alur-langkah-penginstalan-wizard-steps)
2. [Pengecekan Kebutuhan Server (Server Prerequisites)](#pengecekan-kebutuhan-server-server-prerequisites)
3. [Konfigurasi Database Visual](#konfigurasi-database-visual)
4. [Pembuatan Akun Administrator Awal](#pembuatan-akun-administrator-awal)
5. [Penguncian Pasca-Instalasi](#penguncian-pasca-instalasi)

---

## Status

`🟡 Blueprint` — Dokumen dalam tahap perancangan arsitektur.

---

## ⚙️ Kerangka Sistem

### Alur Langkah Penginstalan (Wizard Steps)

*Menjabarkan 5 langkah utama: Sambutan & Lisensi → Pengecekan Sistem → Konfigurasi Database → Pembuatan Admin → Instalasi Selesai.*

### Pengecekan Kebutuhan Server (Server Prerequisites)

*Placeholder: Pemindaian visual apakah versi PHP ≥ 8.2, ekstensi yang dibutuhkan (PDO, mbstring, openssl, tokenizer, xml, ctype, json, bcmath, fileinfo, gd/imagick), dan permission folder (storage/, bootstrap/cache/).*

### Konfigurasi Database Visual

*Placeholder: Formulir penginputan Host DB, Port DB, Nama DB, Username, dan Password DB, dilengkapi tombol tes koneksi instan sebelum melanjutkan ke langkah berikutnya.*

### Pembuatan Akun Administrator Awal

*Placeholder: Form pendaftaran akun superadmin pertama (Nama, Email, Password, Konfirmasi) yang langsung disisipkan setelah migrasi database selesai.*

### Penguncian Pasca-Instalasi

*Placeholder: Mekanisme penguncian installer setelah instalasi berhasil — membuat file lock dan menonaktifkan route installer agar tidak dapat diakses ulang oleh pihak yang tidak berwenang.*

---

## Referensi

- [04_TECH_STACK.md](04_TECH_STACK.md)
- [06_DATABASE_DESIGN.md](06_DATABASE_DESIGN.md)
- [16_SETTING_ENGINE.md](16_SETTING_ENGINE.md)
- [28_CPANEL_DEPLOYMENT.md](28_CPANEL_DEPLOYMENT.md)

## Catatan

- Installer harus berjalan tanpa Composer — semua dependensi sudah di-bundle.
- Installer harus kompatibel dengan shared hosting cPanel yang tidak memiliki akses terminal.
- Setelah instalasi, seluruh route installer harus dinonaktifkan secara permanen.
