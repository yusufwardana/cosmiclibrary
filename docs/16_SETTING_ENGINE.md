# 🌌 16 — Setting Engine

## Deskripsi

Dokumen ini mendesain **Setting Engine** — sistem setelan terpusat yang menyimpan konfigurasi variabel global (Nama Sekolah, Alamat, Aturan Sirkulasi, Konfigurasi SMTP) di dalam database dengan kueri berkinerja tinggi dan mekanisme cache pintar.

## Tujuan

Menyediakan antarmuka pengelolaan konfigurasi dinamis yang memungkinkan administrator mengubah perilaku sistem tanpa menyentuh file `.env` atau kode sumber.

## Ruang Lingkup

- Skema penyimpanan key-value di database
- Sistem cache setelan untuk performa tinggi
- Antarmuka pengaturan admin berbasis tab
- Sinkronisasi runtime dengan konfigurasi Laravel
- API helper `setting('key')` untuk akses mudah dari kode

---

## 🗂️ Table of Contents

1. [Skema Penyimpanan Key-Value](#skema-penyimpanan-key-value)
2. [Sistem Cache Setelan (Cache Layer)](#sistem-cache-setelan-cache-layer)
3. [Antarmuka Pengaturan Admin](#antarmuka-pengaturan-admin)
4. [Sinkronisasi Runtime `.env`](#sinkronisasi-runtime-env)
5. [Helper Function API](#helper-function-api)

---

## Status

`🟡 Blueprint` — Dokumen dalam tahap perancangan arsitektur.

---

## ⚙️ Kerangka Sistem

### Skema Penyimpanan Key-Value

*Struktur tabel `settings` sederhana dengan kolom `key` (varchar, unique), `value` (text), `type` (varchar untuk parsing integer/boolean/array), dan `group` (varchar untuk pengelompokan kategori setelan).*

### Sistem Cache Setelan (Cache Layer)

*Placeholder: Membaca setelan dari cache file Laravel saat boot aplikasi untuk menghindari kueri `SELECT * FROM settings` pada setiap permintaan masuk. Cache di-invalidasi otomatis saat ada perubahan melalui panel admin.*

### Antarmuka Pengaturan Admin

*Placeholder: Desain tabulasi untuk Pengaturan Umum, Aturan Denda & Peminjaman, Setelan SMTP Surat Pengingat, dan Integrasi Eksternal.*

### Sinkronisasi Runtime `.env`

*Placeholder: Mekanisme penimpaan konfigurasi dinamis menggunakan fungsi `config(['mail.host' => ...])` secara aman saat runtime tanpa menulis ulang file `.env`.*

### Helper Function API

*Placeholder: Fungsi global `setting('app.school_name')` yang membaca dari cache terlebih dahulu, lalu fallback ke database. Mendukung default value dan type casting.*

---

## Referensi

- [06_DATABASE_DESIGN.md](06_DATABASE_DESIGN.md)
- [07_CORE_ENGINE.md](07_CORE_ENGINE.md)
- [17_INSTALLER_ENGINE.md](17_INSTALLER_ENGINE.md)

## Catatan

- Semua konfigurasi aplikasi harus disimpan melalui Setting Engine, bukan di-hardcode di file konfigurasi.
- Performa cache harus dioptimalkan untuk shared hosting dengan memori terbatas.
- Mendukung tipe data: `string`, `integer`, `boolean`, `json`, `array`.
