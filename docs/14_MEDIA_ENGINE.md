# 🌌 14 — Media Engine

## Deskripsi

Dokumen ini merancang **Media Engine** — sistem manajemen media yang mengelola penyimpanan, optimasi, dan pembersihan berkas-berkas media (sampul buku, foto anggota, dokumen lampiran) demi efisiensi ruang penyimpanan shared hosting.

## Tujuan

Menyediakan infrastruktur pengelolaan file media yang aman, efisien, dan mudah digunakan, dengan fokus pada optimasi ruang disk di lingkungan shared hosting.

## Ruang Lingkup

- Sistem penyimpanan berkas (storage drivers)
- Optimasi gambar otomatis (kompresi, resize)
- Kebijakan kebersihan disk (garbage collection)
- Keamanan akses file media
- Upload dan validasi file

---

## 🗂️ Table of Contents

1. [Sistem Penyimpanan Berkas (Storage Drivers)](#sistem-penyimpanan-berkas-storage-drivers)
2. [Optimasi Gambar Otomatis (Image Optimization)](#optimasi-gambar-otomatis-image-optimization)
3. [Kebijakan Kebersihan Disk (Disk Hygiene)](#kebijakan-kebersihan-disk-disk-hygiene)
4. [Skema URL Publik & Keamanan Dokumen](#skema-url-publik--keamanan-dokumen)
5. [Upload & Validasi File](#upload--validasi-file)

---

## Status

`🟡 Blueprint` — Dokumen dalam tahap perancangan arsitektur.

---

## ⚙️ Kerangka Sistem

### Sistem Penyimpanan Berkas (Storage Drivers)

*Placeholder: Konfigurasi default menggunakan driver `local` Laravel yang memetakan file ke direktori `/storage/app/public` terhubung ke `/public/storage` via symlink. Untuk cPanel tanpa symlink support, disediakan script PHP alternatif.*

### Optimasi Gambar Otomatis (Image Optimization)

*Placeholder: Pemrosesan gambar unggahan menggunakan pustaka Intervention Image atau GD untuk mengompres ukuran file JPG/PNG di bawah 200KB. Otomatis membuat thumbnail (150×150) dan medium (600×400) dari gambar asli.*

### Kebijakan Kebersihan Disk (Disk Hygiene)

*Placeholder: Skrip pembersih (garbage collector) terjadwal untuk menghapus berkas-berkas yang tidak lagi dirujuk di database (orphaned files). Berjalan via Laravel scheduler dengan laporan hasil pembersihan.*

### Skema URL Publik & Keamanan Dokumen

*Placeholder: File publik (cover buku, foto profil) diakses via URL publik. File sensitif (laporan keuangan, data ekspor) disimpan di luar public path dan diakses via route terproteksi dengan permission check.*

### Upload & Validasi File

*Placeholder: Validasi saat upload: tipe MIME, ekstensi file, ukuran maksimum (dikonfigurasi via Setting Engine). File di-rename dengan hash unik untuk keamanan. Metadata file disimpan di tabel `media`.*

---

## Referensi

- [07_CORE_ENGINE.md](07_CORE_ENGINE.md)
- [22_SECURITY_GUIDELINE.md](22_SECURITY_GUIDELINE.md)
- [16_SETTING_ENGINE.md](16_SETTING_ENGINE.md)
- [18_BACKUP_ENGINE.md](18_BACKUP_ENGINE.md)

## Catatan

- Optimasi ruang disk sangat penting untuk shared hosting dengan quota terbatas.
- Jangan simpan file media di database (BLOB) — gunakan filesystem.
- Semua file upload harus divalidasi sisi server, jangan percaya ekstensi file saja.
