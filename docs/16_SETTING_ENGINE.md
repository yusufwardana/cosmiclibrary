# 🌌 15_SYSTEM_SETTING.md

## 🎯 Tujuan (Goal)
Dokumen ini mendesain Sistem Setelan (System Setting) yang menyimpan konfigurasi variabel global (Nama Sekolah, Alamat, Aturan Sirkulasi, Konfigurasi SMTP) di dalam database dengan kueri berkinerja tinggi.

---

## 🗂️ Table of Contents
1. [Skema Penyimpanan Key-Value](#skema-penyimpanan-key-value)
2. [Sistem Cache Setelan (Cache Layer)](#sistem-cache-setelan-cache-layer)
3. [Antarmuka Pengaturan Admin](#antarmuka-pengaturan-admin)
4. [Sinkronisasi Runtime `.env`](#sinkronisasi-runtime-env)

---

## ⚙️ Placeholder & Kerangka Sistem

### Skema Penyimpanan Key-Value
*Struktur tabel `settings` sederhana dengan kolom `key` (varchar, unique), `value` (text), dan `type` (varchar untuk parsing integer/boolean/array).*

### Sistem Cache Setelan (Cache Layer)
*Placeholder: Membaca setelan dari cache file Laravel saat boot aplikasi untuk menghindari kueri `SELECT * FROM settings` pada setiap permintaan masuk.*

### Antarmuka Pengaturan Admin
*Placeholder: Desain tabulasi untuk Pengaturan Umum, Aturan Denda & Peminjaman, Setelan SMTP Surat Pengingat, dan Integrasi.*

### Sinkronisasi Runtime `.env`
*Placeholder: Mekanisme penimpaan konfigurasi dinamis menggunakan fungsi `config(['mail.host' => ...])` secara aman saat runtime.*
