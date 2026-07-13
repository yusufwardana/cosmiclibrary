# 📝 CosmicLib Project Manifest

Berkas ini mendokumentasikan spesifikasi teknis, lingkungan target, arsitektur dasar, dan standar pengembangan untuk CosmicLib Engine.

---

## 🏗️ Metadata Proyek

| Atribut | Nilai Spesifikasi |
| :--- | :--- |
| **Nama Engine** | CosmicLib Engine |
| **Nama Produk Utama** | CosmicLib Library (Sistem Informasi Perpustakaan SMA) |
| **Target Framework** | Laravel 12.x (PHP >= 8.2) |
| **Arsitektur Frontend** | Blade Templates + Bootstrap 5 + Vite Asset Bundling |
| **Mesin Database** | MySQL >= 8.0 / MariaDB >= 10.4 |
| **Target Hosting** | Shared Hosting cPanel (Dengan ketersediaan Terminal & Node.js) |
| **Bahasa Antarmuka (UI)** | Bahasa Indonesia |
| **Status Pengembangan** | Fase 1: Inisialisasi & Cetak Biru (Blueprint Initialized) |

---

## 🏛️ Desain Arsitektur & Pola Sistem
Untuk menjamin modularitas dan performa optimal pada shared hosting, sistem ini menggunakan pola arsitektur berikut:

1. **Modular Monolith**: Fitur-fitur utama dipecah menjadi modul-modul logis (misalnya: Modul Buku, Modul Sirkulasi, Modul Anggota). Setiap modul mengelompokkan logika controller, database, dan view-nya sendiri untuk menghindari penumpukan berkas pada folder core Laravel.
2. **Service Layer Pattern**: Pengontrol (Controllers) tidak boleh menulis kueri database secara langsung atau memproses logika bisnis yang rumit. Logika bisnis wajib diekstraksi ke dalam berkas layanan independen (`Services/`) agar dapat digunakan kembali oleh API maupun CLI Command.
3. **Optimasi Shared Hosting**:
   - Menghindari kueri database N+1 dengan memanfaatkan eager loading (`with()`).
   - Melakukan caching untuk data konfigurasi sistem yang jarang berubah menggunakan Redis/File cache.
   - Menyediakan jalur manual tanpa Composer (melalui installer bawaan) untuk ekstraksi modul jika terminal SSH cPanel dibatasi oleh penyedia hosting.

---

## 📌 Standar Penulisan Kode (Coding Standards)

- **Gaya Penulisan PHP**: Mengikuti panduan **PSR-12 (Extended Coding Style Guide)**.
- **Kebijakan Bahasa Penulisan**:
  - **User Interface**: Wajib menggunakan Bahasa Indonesia yang formal, ramah pengguna, dan bebas dari istilah teknis membingungkan bagi pustakawan sekolah.
  - **Sintaks Kode**: Kelas, variabel, method, skema database, migrasi, dan dokumentasi API wajib ditulis dalam **Bahasa Inggris** untuk menjaga kompatibilitas penyorotan sintaksis dan integrasi pustaka pihak ketiga.
- **Prinsip SOLID**: Setiap kelas harus memiliki satu tanggung jawab yang terdefinisi dengan jelas (Single Responsibility Principle).

---

## 🤖 Aturan Pengembangan Berbasis Kecerdasan Buatan (AI Development Rules)
Ketika mengembangkan CosmicLib menggunakan AI Coding Assistant, patuhi panduan berikut:

1. **Prinsip "Pristine Craftsmanship"**: Hindari penulisan kode berulang atau menambahkan fitur yang tidak diminta. Selesaikan tugas spesifik dengan baris kode seefisien mungkin.
2. **Kepatuhan Terhadap Cetak Biru**: Jangan membuat model, kontroler, atau tabel database secara serampangan. Selalu rujuk skema yang telah disepakati di dalam direktori `blueprint/` dan `docs/06_DATABASE_DESIGN.md`.
3. **Penanganan Error Terstruktur**: Setiap fungsi atau tindakan database yang sensitif wajib dibungkus dalam blok `try-catch`, dicatat dalam log Laravel (`Log::error`), dan mengembalikan respon kesalahan yang ramah kepada pengguna akhir.
