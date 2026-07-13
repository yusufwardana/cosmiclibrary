# 🌌 14_MEDIA_ENGINE.md

## 🎯 Tujuan (Goal)
Dokumen ini mendesain Media Engine yang mengelola penyimpanan, optimasi, pembersihan berkas-berkas media (sampul buku, foto anggota, dokumen lampiran) demi efisiensi ruang penyimpanan shared hosting.

---

## 🗂️ Table of Contents
1. [Sistem Penyimpanan Berkas (Storage Drivers)](#sistem-penyimpanan-berkas-storage-drivers)
2. [Optimasi Gambar Otomatis (Image Optimization)](#optimasi-gambar-otomatis-image-optimization)
3. [Kebijakan Kebersihan Disk (Disk Hygiene)](#kebijakan-kebersihan-disk-disk-hygiene)
4. [Skema URL Publik & Keamanan Dokumen](#skema-url-publik--keamanan-dokumen)

---

## ⚙️ Placeholder & Kerangka Sistem

### Sistem Penyimpanan Berkas (Storage Drivers)
*Konfigurasi default menggunakan driver `local` Laravel yang memetakan file ke direktori `/storage/app/public` terhubung ke `/public/storage` via symlink.*

### Optimasi Gambar Otomatis (Image Optimization)
*Placeholder: Pemrosesan gambar unggahan menggunakan pustaka Intervenstion Image atau GD untuk mengompres ukuran file JPG/PNG di bawah 200KB.*

### Kebijakan Kebersihan Disk (Disk Hygiene)
*Placeholder: Skrip pembersih (garbage collector) untuk menghapus berkas-berkas sampah yang tidak lagi dirujuk di database (misal foto anggota yang sudah dihapus).*

### Skema URL Publik & Keamanan Dokumen
*Placeholder: Pembatasan akses langsung ke berkas sensitif seperti laporan keuangan/denda perpustakaan.*
