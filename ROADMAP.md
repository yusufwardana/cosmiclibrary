# 🗺️ CosmicLib Engine & Library Roadmap

Dokumen ini menjelaskan peta jalan (roadmap) pengembangan teknis jangka pendek, menengah, dan panjang untuk **CosmicLib Engine** dan produk pertamanya, **CosmicLib Library**.

---

## 📅 Fase 1: Cetak Biru, Struktur, & Standarisasi (Sedang Berjalan)
**Target Terpenuhi**: Pengondisian repositori dan penyusunan basis pemahaman AI.
- [x] Inisialisasi struktur direktori repositori profesional.
- [x] Pembuatan 21 dokumen fondasi sistem (`docs/`).
- [x] Penyusunan file manifes proyek (`PROJECT_MANIFEST.md`) dan pedoman instruksi AI (`AGENTS.md`, `CLAUDE.md`, `AI_STUDIO.md`, dll).
- [x] Pembuatan antarmuka visual peninjau dokumentasi (Dokumentasi Dasbor) untuk mempermudah eksplorasi lokal sebelum migrasi ke Laravel.

---

## 📅 Fase 2: Core Engine & Sistem Integrasi Dasar (Q3 2026)
**Fokus Utama**: Membangun kerangka modular dasar Laravel 12 dan fungsionalitas sistem inti.
- [ ] **Setup Framework**: Instalasi Laravel 12.x segar dan konfigurasi Vite + Bootstrap 5.
- [ ] **Core Theme Engine**: Sistem pemuatan tema berbasis Blade, memungkinkan peralihan layout atau penambahan stylesheet kustom sekolah secara dinamis.
- [ ] **Database & Migrasi Dasar**: Pembuatan skema tabel pengguna, perizinan, menu, dan setelan sistem (`06_DATABASE_DESIGN.md`).
- [ ] **Module Engine**: Implementasi loader modul pintar untuk memindai, mendaftarkan, mengaktifkan, dan menonaktifkan modul tambahan secara dinamis.
- [ ] **System ACL (Access Control List)**: Fungsionalitas manajemen peran (role-based access) untuk Admin Sekolah, Pustakawan, Guru, Siswa, dan Pengunjung Umum.

---

## 📅 Fase 3: Modul Sirkulasi & Manajemen Buku (Q4 2026)
**Fokus Utama**: Implementasi fungsionalitas utama aplikasi perpustakaan.
- [ ] **Manajemen Anggota**: Sinkronisasi data siswa dengan kelas kustom dan pencetakan kartu anggota ber-barcode otomatis.
- [ ] **Katalogisasi Buku (Katalog)**: Integrasi pencarian ISBN via API terbuka, pengklasifikasian buku berbasis sistem klasifikasi persepuluhan Dewey (DDC).
- [ ] **Alur Sirkulasi (Transaksi)**:
  - Transaksi peminjaman buku (dilengkapi batasan jumlah pinjam).
  - Transaksi pengembalian buku terintegrasi dengan kalkulasi otomatis denda per hari keterlambatan.
  - Perpanjangan masa pinjam buku mandiri atau lewat pustakawan.
- [ ] **Sistem Notifikasi**: Pengiriman pengingat keterlambatan via email atau integrasi gateway WhatsApp kustom.

---

## 📅 Fase 4: Analitik, Utilitas, & Penyempurnaan (Q1 2027)
**Fokus Utama**: Penyusunan pelaporan, backup, dan kemudahan penyebaran (deployment).
- [ ] **Installer Web Interaktif**: Halaman penyiapan database, pembuatan akun administrator pertama, dan pemeriksaan spesifikasi server shared hosting secara visual.
- [ ] **Pelaporan Tingkat Tinggi**: Visualisasi data statistik perpustakaan menggunakan chart (buku terpopuler, keaktifan peminjaman kelas, riwayat denda terkumpul).
- [ ] **Backup & Restore**: Modul pencadangan database MySQL dan berkas media dalam format `.zip` langsung dari panel admin perpustakaan.
- [ ] **Modul Update Otomatis**: Integrasi API penarik update zip dari repositori rilis resmi untuk mempermudah pembaruan di shared hosting cPanel.

---

## 📅 Fase 5: Distribusi & Pemeliharaan (Q2 2027 dan Seterusnya)
- [ ] Rilis Resmi CosmicLib Library v1.0.0.
- [ ] Pembuatan dokumentasi panduan pengguna akhir (pustakawan dan siswa) berupa PDF dan video tutorial.
- [ ] Pembukaan ekosistem plugin untuk kontribusi komunitas pengembang sekolah.
