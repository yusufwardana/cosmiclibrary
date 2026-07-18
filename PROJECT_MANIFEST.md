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
| **Node.js** | >= 18.x (untuk Vite build tools) |
| **Bahasa Antarmuka (UI)** | Bahasa Indonesia |
| **Architecture** | Modular CMS (Modular Monolith) |
| **Version** | 1.0.0-alpha |
| **Status** | Blueprint — Fase 1: Inisialisasi & Cetak Biru |

---

## 🏛️ Desain Arsitektur & Pola Sistem

Untuk menjamin modularitas dan performa optimal pada shared hosting, sistem ini menggunakan pola arsitektur berikut:

1. **Modular Monolith**: Fitur-fitur utama dipecah menjadi modul-modul logis (misalnya: Modul Buku, Modul Sirkulasi, Modul Anggota). Setiap modul mengelompokkan logika controller, database, dan view-nya sendiri untuk menghindari penumpukan berkas pada folder core Laravel.
2. **Service Layer Pattern**: Pengontrol (Controllers) tidak boleh menulis kueri database secara langsung atau memproses logika bisnis yang rumit. Logika bisnis wajib diekstraksi ke dalam berkas layanan independen (`Services/`) agar dapat digunakan kembali oleh API maupun CLI Command.
3. **Optimasi Shared Hosting**:
   - Menghindari kueri database N+1 dengan memanfaatkan eager loading (`with()`).
   - Melakukan caching untuk data konfigurasi sistem yang jarang berubah menggunakan file cache.
   - Menyediakan jalur manual tanpa Composer (melalui installer bawaan) untuk ekstraksi modul jika terminal SSH cPanel dibatasi oleh penyedia hosting.

---

## 🔧 Engine Inti

| Engine | Dokumen | Fungsi |
| :--- | :--- | :--- |
| Core Engine | [07_CORE_ENGINE.md](docs/07_CORE_ENGINE.md) | Lifecycle, DI, service providers |
| Module Engine | [08_MODULE_ENGINE.md](docs/08_MODULE_ENGINE.md) | Loading & manajemen modul |
| Theme Engine | [09_THEME_ENGINE.md](docs/09_THEME_ENGINE.md) | Manajemen tema visual |
| Permission Engine | [10_PERMISSION_ENGINE.md](docs/10_PERMISSION_ENGINE.md) | ACL berbasis role |
| Menu Engine | [11_MENU_ENGINE.md](docs/11_MENU_ENGINE.md) | Navigasi dinamis |
| Widget Engine | [12_WIDGET_ENGINE.md](docs/12_WIDGET_ENGINE.md) | Dashboard widgets |
| Plugin Engine | [13_PLUGIN_ENGINE.md](docs/13_PLUGIN_ENGINE.md) | Ekstensi pihak ketiga |
| Media Engine | [14_MEDIA_ENGINE.md](docs/14_MEDIA_ENGINE.md) | Penyimpanan & optimasi file |
| Notification Engine | [15_NOTIFICATION_ENGINE.md](docs/15_NOTIFICATION_ENGINE.md) | Email, WhatsApp, in-app |
| Setting Engine | [16_SETTING_ENGINE.md](docs/16_SETTING_ENGINE.md) | Konfigurasi key-value |
| Installer Engine | [17_INSTALLER_ENGINE.md](docs/17_INSTALLER_ENGINE.md) | Web installer wizard |
| Backup Engine | [18_BACKUP_ENGINE.md](docs/18_BACKUP_ENGINE.md) | Backup & restore |
| Update Engine | [19_UPDATE_ENGINE.md](docs/19_UPDATE_ENGINE.md) | Pembaruan otomatis |
| License Engine | [20_LICENSE_ENGINE.md](docs/20_LICENSE_ENGINE.md) | Lisensi & aktivasi |

---

## 📌 Standar Penulisan Kode (Coding Standards)

- **Gaya Penulisan PHP**: Mengikuti panduan **PSR-12 (Extended Coding Style Guide)**.
- **Kebijakan Bahasa Penulisan**:
  - **User Interface**: Wajib menggunakan Bahasa Indonesia yang formal, ramah pengguna, dan bebas dari istilah teknis membingungkan bagi pustakawan sekolah.
  - **Sintaks Kode**: Kelas, variabel, method, skema database, migrasi, dan dokumentasi API wajib ditulis dalam **Bahasa Inggris**.
- **Prinsip SOLID**: Setiap kelas harus memiliki satu tanggung jawab yang terdefinisi dengan jelas.
- **Service Layer**: Logika bisnis di Service, bukan Controller.
- **Dilarang Keras**:
  - Hardcode role, permission, menu, atau warna
  - `env()` di luar file config
  - Raw SQL tanpa parameter binding
  - `dd()` atau `dump()` di production code

---

## 🤖 Aturan Pengembangan Berbasis AI

Ketika mengembangkan CosmicLib menggunakan AI Coding Assistant, patuhi panduan berikut:

1. **Prinsip "Pristine Craftsmanship"**: Hindari penulisan kode berulang. Selesaikan tugas spesifik dengan baris kode seefisien mungkin.
2. **Kepatuhan Terhadap Cetak Biru**: Jangan membuat model, kontroler, atau tabel database secara serampangan. Selalu rujuk skema di `blueprint/` dan `docs/`.
3. **Penanganan Error Terstruktur**: Setiap fungsi yang sensitif wajib dibungkus `try-catch`, dicatat dalam log Laravel (`Log::error`), dan mengembalikan respon yang ramah pengguna.
4. **Baca Dokumentasi Dulu**: AI wajib membaca `AGENTS.md` dan `PROJECT_MANIFEST.md` sebelum menulis kode.
5. **Rencana Sebelum Kode**: Analisis dan buat rencana implementasi sebelum menghasilkan kode.
