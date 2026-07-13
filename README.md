# 🌌 CosmicLib Engine

```text
   ______                      _      _       _      ______                 _              
  / _____)                    (_)    | |     | |    |  ____)               (_)             
 | /       ___   ___ ____ ___  _  ___| |      | |   | |___  ____   ____ _ ___  ____  _____ 
 | |      / _ \ /___)    _ _ \| |/ ___) |     | |   |  ___)|  _ \ / _  | |  _ \|  _ \| ___ |
 | \_____( |_| |___ | | | | | | | (___| |_____| |___| |____| | | ( (_| | | | | | | | | ____|
  \______)___/(___/|_|_|_|_|_|_|_|____)_______)_____|______)_| |_|\___ |_|_| |_|_| |_|_____)
                                                                 (_____|                   
```

> **CosmicLib Engine** adalah sebuah *core-engine* modular yang dirancang untuk menjadi pondasi tangguh Sistem Informasi Perpustakaan sekolah tingkat menengah (SMA/SMK) di Indonesia. Produk perdana yang ditenagai oleh engine ini adalah **CosmicLib Library**.

---

## 🌟 Filosofi Desain
CosmicLib dirancang dengan tiga pilar utama:
1. **Modularitas Tanpa Batas (Extensible Modularity)**: Setiap fitur sirkulasi, manajemen katalog, denda, hingga integrasi luar adalah modul mandiri yang dapat diaktifkan, dinonaktifkan, atau diperbarui secara independen tanpa merusak kestabilan core engine.
2. **Kinerja Maksimal pada Resource Minimal**: Dioptimalkan secara khusus agar dapat berjalan sangat cepat bahkan saat dideploy pada layanan **Shared Hosting cPanel** yang memiliki keterbatasan RAM dan CPU.
3. **Kemudahan Pengoperasian Sekolah (High Usability)**: Alur kerja disesuaikan secara presisi dengan kebutuhan administrasi sekolah menengah di Indonesia, menggunakan standar perpustakaan nasional dan bahasa Indonesia yang santun serta mudah dipahami oleh pustakawan sekolah maupun siswa.

---

## 🗺️ Roadmap Utama
- [x] **Fase 1: Inisialisasi & Blueprint (Current)** - Penyusunan struktur repository, standarisasi kode, cetak biru database, dan kerangka arsitektur.
- [ ] **Fase 2: Core Engine & Installer** - Pengembangan sistem manajemen tema, perizinan pengguna (ACL), pendaftaran menu dinamis, dan installer web interaktif.
- [ ] **Fase 3: Modul Dasar Perpustakaan** - Implementasi manajemen anggota (siswa & guru), katalogisasi buku (klasifikasi DDC), dan inventori buku fisik.
- [ ] **Fase 4: Modul Sirkulasi & Denda** - Transaksi peminjaman, pengembalian, perpanjangan, serta aturan denda keterlambatan terotomatisasi.
- [ ] **Fase 5: Integrasi & Laporan** - Cetak kartu anggota dengan barcode/QR, pelaporan statistik perpustakaan bulanan/tahunan format Kemendikbud, serta ekspor-impor Excel/PDF.
- [ ] **Fase 6: Rilis CosmicLib Library v1.0.0** - Uji coba beta di sekolah-sekolah sasaran, dokumentasi lengkap, dan penyesuaian stabilitas hosting.

---

## 📂 Struktur Project Repository
Repository ini ditata secara profesional dengan pembagian direktori sebagai berikut:

```text
├── .github/              # Template isu, PR, dan otomatisasi workflow GitHub
├── docs/                 # Dokumentasi arsitektur sistem (00 sampai 20)
├── blueprint/            # Skema database, API spec, dan cetak biru arsitektur
├── prompts/              # Kumpulan instruksi dan rekayasa prompt untuk AI coding assistants
│   ├── claude/           # Prompt khusus untuk Claude AI
│   ├── codex/            # Prompt khusus untuk GitHub Copilot / Codex
│   ├── cline/            # Prompt khusus untuk Cline
│   └── ai-studio/        # Prompt khusus untuk Google AI Studio
├── examples/             # Contoh implementasi modul, view, atau skrip pembantu
├── assets/               # Berkas visual, logo, mockup UI, dan icon
├── scripts/              # Skrip pembantu instalasi, backup, migrasi, dan deployment
├── tests/                # Kerangka pengujian otomatis unit, fitur, dan integrasi
└── root files            # Dokumen deklaratif proyek (README, LICENSE, ROADMAP, dll)
```

---

## 🚀 Cara Memulai (Penyusunan Awal)
Karena repository ini berada pada fase inisialisasi blueprint dan dokumentasi, berikut langkah-langkah untuk mempersiapkan pengembangan:

1. **Eksplorasi Dokumentasi**: Pelajari berkas-berkas arsitektur di dalam folder `docs/`.
2. **Review Cetak Biru (Blueprint)**: Periksa rancangan database di folder `blueprint/`.
3. **Gunakan Panduan AI**: Jika Anda mengembangkan proyek ini menggunakan AI Coding Assistant (seperti Gemini, Claude, atau Cline), bacalah instruksi integrasi di berkas `AGENTS.md`, `CLAUDE.md`, atau `AI_STUDIO.md` agar standar penulisan kode tetap terjaga secara otomatis.

---

## 📄 Lisensi (License)
Proyek ini dilisensikan di bawah **MIT License**. Lihat berkas [LICENSE](LICENSE) untuk detail lebih lanjut.
