# CosmicLib Engine

```text
   ______                      _      _       _      ______                 _              
  / _____)                    (_)    | |     | |    |  ____)               (_)             
 | /       ___   ___ ____ ___  _  ___| |      | |   | |___  ____   ____ _ ___  ____  _____ 
 | |      / _ \ /___)    _ _ \| |/ ___) |     | |   |  ___)|  _ \ / _  | |  _ \|  _ \| ___ |
 | \_____( |_| |___ | | | | | | | (___| |_____| |___| |____| | | ( (_| | | | | | | | | ____|
  \______)___/(___/|_|_|_|_|_|_|_|____)_______)_____|______)_| |_|\___ |_|_| |_|_| |_|_____)
                                                                  (_____|                   
```

<!-- Logo Placeholder: Ganti blok ASCII di atas dengan logo resmi saat tersedia -->

> **CosmicLib Engine** adalah *core-engine* CMS modular berbasis Laravel 12 yang dirancang untuk menjadi pondasi tangguh aplikasi pendidikan. Produk pertama yang ditenagai oleh engine ini adalah **CosmicLib Library** — Sistem Informasi Perpustakaan SMA/SMK di Indonesia.

---

## 🌟 Visi

Menjadi platform CMS modular terdepan untuk aplikasi pendidikan di Indonesia, memberdayakan setiap sekolah dengan teknologi perpustakaan digital yang **modern**, **terjangkau**, dan **mudah digunakan**.

## 🎯 Misi

1. Membangun framework CMS modular yang extensible dan maintainable berbasis Laravel 12.
2. Menyediakan solusi perpustakaan SMA yang lengkap dan optimal di shared hosting cPanel.
3. Menurunkan hambatan teknologi bagi sekolah-sekolah di Indonesia.
4. Membangun ekosistem plugin dan tema yang terbuka untuk kontribusi komunitas.

## 💡 Filosofi

CosmicLib dirancang dengan tiga pilar utama:

1. **Modularitas Tanpa Batas (Extensible Modularity)**: Setiap fitur — sirkulasi, katalog, denda, hingga integrasi luar — adalah modul mandiri yang dapat diaktifkan, dinonaktifkan, atau diperbarui secara independen tanpa merusak kestabilan core engine.
2. **Kinerja Maksimal pada Resource Minimal**: Dioptimalkan secara khusus agar berjalan cepat bahkan pada layanan **Shared Hosting cPanel** dengan keterbatasan RAM dan CPU.
3. **Kemudahan Pengoperasian Sekolah (High Usability)**: Alur kerja disesuaikan dengan kebutuhan administrasi sekolah menengah di Indonesia, menggunakan standar perpustakaan nasional dan Bahasa Indonesia yang santun.

## 🎯 Target

- Perpustakaan SMA/SMK di Indonesia
- Pustakawan sekolah yang tidak memiliki latar belakang IT mendalam
- Sekolah dengan shared hosting cPanel sebagai satu-satunya infrastruktur server
- Komunitas pengembang yang ingin berkontribusi modul dan plugin pendidikan

---

## ✨ Fitur Utama

| Fitur | Deskripsi |
|:---|:---|
| 📚 **Katalogisasi Buku** | Manajemen katalog buku dengan klasifikasi DDC dan pencarian ISBN |
| 🔄 **Sirkulasi** | Peminjaman, pengembalian, dan perpanjangan buku |
| 💳 **Kartu Anggota** | Pencetakan kartu anggota ber-barcode/QR code |
| 💰 **Denda Otomatis** | Kalkulasi denda keterlambatan terotomatisasi |
| 📊 **Laporan & Statistik** | Laporan peminjaman, denda, dan statistik bulanan/tahunan |
| 🔔 **Notifikasi** | Pengingat via email dan WhatsApp gateway |
| 🎨 **Theme Engine** | Kustomisasi tampilan per sekolah |
| 🧩 **Module Engine** | Arsitektur modular — pasang/lepas fitur dinamis |
| 🔐 **Permission Engine** | Role-based ACL untuk manajemen akses |
| 🖥️ **Installer Web** | Instalasi tanpa terminal — cukup via browser |
| 💾 **Backup & Restore** | Pencadangan dan pemulihan data otomatis |

---

## 🗺️ Roadmap Utama

- [x] **Fase 1: Inisialisasi & Blueprint** *(Saat Ini)* — Penyusunan struktur repository, standarisasi kode, cetak biru database, dan kerangka arsitektur.
- [ ] **Fase 2: Core Engine & Installer** — Pengembangan sistem manajemen tema, perizinan (ACL), menu dinamis, dan installer web interaktif.
- [ ] **Fase 3: Modul Perpustakaan** — Katalogisasi buku, manajemen anggota, dan inventori buku fisik.
- [ ] **Fase 4: Sirkulasi & Denda** — Transaksi peminjaman, pengembalian, perpanjangan, denda otomatis.
- [ ] **Fase 5: Integrasi & Laporan** — Kartu anggota barcode/QR, pelaporan Kemendikbud, ekspor PDF/Excel.
- [ ] **Fase 6: Rilis v1.0.0** — Uji coba beta, dokumentasi lengkap, stabilisasi hosting.

---

## 🛠️ Teknologi

| Komponen | Teknologi |
|:---|:---|
| Backend | PHP 8.3 + Laravel 12 |
| Frontend | Blade + Bootstrap 5 + Vite |
| Database | MySQL 8.0 / MariaDB 10.4 |
| Hosting | Shared Hosting cPanel |
| Architecture | Modular Monolith CMS |

## 🏗️ Arsitektur

CosmicLib menggunakan **Modular Monolith Architecture** dengan engine-engine inti:

```text
┌──────────────────────────────────────────────┐
│                CosmicLib Engine               │
├──────────────┬──────────────┬────────────────┤
│ Theme Engine │ Module Engine│ Plugin Engine  │
├──────────────┼──────────────┼────────────────┤
│ Menu Engine  │ Permission   │ Widget Engine  │
│              │ Engine       │                │
├──────────────┼──────────────┼────────────────┤
│ Setting      │ Media Engine │ Notification   │
│ Engine       │              │ Engine         │
├──────────────┴──────────────┴────────────────┤
│              Core Engine (Laravel 12)         │
└──────────────────────────────────────────────┘
```

---

## 📂 Struktur Repository

```text
├── .github/              # Template isu, PR, dan workflow CI/CD
├── docs/                 # 33 dokumen arsitektur sistem (00–32)
├── blueprint/            # Skema database dan cetak biru arsitektur
├── prompts/              # Template prompt per AI coding assistant
│   ├── claude/
│   ├── codex/
│   ├── cline/
│   ├── chatgpt/
│   └── ai-studio/
├── examples/             # Contoh implementasi modul
├── assets/               # Logo, mockup, icon
├── scripts/              # Skrip pembantu instalasi dan deployment
├── tests/                # Kerangka pengujian otomatis
└── root files            # README, ROADMAP, LICENSE, AI instructions
```

---

## 🚀 Cara Memulai

Karena repository ini berada pada **Fase 1 (Blueprint & Dokumentasi)**, berikut langkah-langkah untuk memulai:

1. **📖 Eksplorasi Dokumentasi**: Pelajari berkas-berkas arsitektur di folder [`docs/`](docs/).
2. **🔍 Review Cetak Biru**: Periksa rancangan database di folder [`blueprint/`](blueprint/).
3. **🤖 Panduan AI**: Jika menggunakan AI Coding Assistant, baca instruksi di:
   - [`AGENTS.md`](AGENTS.md) — Aturan universal semua AI
   - [`CLAUDE.md`](CLAUDE.md) — Khusus Claude
   - [`CODEX.md`](CODEX.md) — Khusus Codex
   - [`AI_STUDIO.md`](AI_STUDIO.md) — Khusus AI Studio
4. **📋 Baca Manifest**: Pahami spesifikasi teknis di [`PROJECT_MANIFEST.md`](PROJECT_MANIFEST.md).

---

## 📚 Dokumentasi

Dokumentasi lengkap tersedia di folder [`docs/`](docs/) dengan 33 dokumen terstruktur:

| Kategori | Dokumen |
|:---|:---|
| **Fondasi** | System Prompt, Project Overview, Vision, Architecture |
| **Teknis** | Tech Stack, Folder Structure, Database Design/Schema |
| **Engine** | Core, Module, Theme, Permission, Menu, Widget, Plugin, Media, Notification, Setting, Installer, Backup, Update, License |
| **Panduan** | API Guideline, Security, Coding Standard, UI Guideline, AI Guideline |
| **Deployment** | Deployment, cPanel Deployment |
| **Perencanaan** | Roadmap, Release Plan, Prompt Library, Library Module |

---

## 🤝 Kontribusi

Kami menyambut kontribusi dari komunitas! Silakan baca:
- [Panduan Kontribusi](.github/CONTRIBUTING.md)
- [Kode Etik](.github/CODE_OF_CONDUCT.md)
- [Kebijakan Keamanan](.github/SECURITY.md)

---

## 📄 Lisensi

Proyek ini dilisensikan di bawah **MIT License**. Lihat berkas [LICENSE](LICENSE) untuk detail lebih lanjut.

---

<p align="center">
  <strong>CosmicLib Engine</strong> — Membangun masa depan perpustakaan sekolah Indonesia 🚀
</p>
