# 🌌 17_SYSTEM_UPDATE.md

## 🎯 Tujuan (Goal)
Dokumen ini mendesain Sistem Pembaruan (System Update Engine) yang memungkinkan administrator perpustakaan sekolah menarik dan menerapkan pembaruan versi CosmicLib secara otomatis dari repositori resmi via ZIP extraction.

---

## 🗂️ Table of Contents
1. [Mekanisme Cek Versi Terbaru](#mekanisme-cek-versi-terbaru)
2. [Alur Unduh & Ekstraksi ZIP Terenkripsi](#alur-unduh--ekstraksi-zip-terenkritsi)
3. [Migrasi Database Pasca-Pembaruan](#migrasi-database-pasca-pembaruan)
4. [Penanganan Rollback Otomatis (Fail-Safe Rollback)](#penanganan-rollback-otomatis-fail-safe-rollback)

---

## ⚙️ Placeholder & Kerangka Sistem

### Mekanisme Cek Versi Terbaru
*Sistem memicu request API server berkala ke server rilis resmi CosmicLib untuk membandingkan nomor versi lokal dengan versi rilis stabil terbaru.*

### Alur Unduh & Ekstraksi ZIP Terenkripsi
*Placeholder: Unduh paket pembaruan dalam format ZIP ke folder penyimpanan sementara `/storage/app/updates` dan proses ekstraksi aman menimpa file usang.*

### Migrasi Database Pasca-Pembaruan
*Placeholder: Penjalanan otomatis kueri `php artisan migrate` via program internal setelah file baru berhasil diekstrak.*

### Penanganan Rollback Otomatis (Fail-Safe Rollback)
*Placeholder: Pembuatan salinan cadangan file sebelum diperbarui, jika terjadi kegagalan sistem akan otomatis memulihkan kondisi sebelum update.*
