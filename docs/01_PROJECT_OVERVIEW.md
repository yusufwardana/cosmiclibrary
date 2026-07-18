# 🌌 CosmicLib Engine — Project Overview

> **Dokumen konseptual** — Gambaran menyeluruh CosmicLib Engine bagi developer, AI, dan stakeholder.
>
> Baca dokumen ini **sebelum** dokumen arsitektur atau spesifikasi teknis lainnya.

| Atribut | Nilai |
| :--- | :--- |
| **Dokumen** | `docs/01_PROJECT_OVERVIEW.md` |
| **Versi** | 1.0 |
| **Status** | `🟡 Blueprint` — Fase 1: Inisialisasi & Cetak Biru |
| **Sifat** | Konseptual (bukan teknis mendalam) |
| **Bahasa** | Bahasa Indonesia |

---

## 🗂️ Daftar Isi

1. [Pendahuluan](#1-pendahuluan)
2. [Latar Belakang](#2-latar-belakang)
3. [Tujuan](#3-tujuan)
4. [Visi](#4-visi)
5. [Misi](#5-misi)
6. [Core Values](#6-core-values)
7. [Produk CosmicLib](#7-produk-cosmiclib)
8. [Target Pengguna](#8-target-pengguna)
9. [Lingkup Proyek](#9-lingkup-proyek)
10. [Target Platform](#10-target-platform)
11. [Teknologi](#11-teknologi)
12. [Prinsip Pengembangan](#12-prinsip-pengembangan)
13. [AI Development](#13-ai-development)
14. [Roadmap](#14-roadmap)
15. [Kesimpulan](#15-kesimpulan)

---

## 1. Pendahuluan

**CosmicLib Engine** adalah fondasi (core engine) CMS modular berbasis Laravel yang dirancang untuk membangun aplikasi pendidikan di Indonesia. Engine ini menyediakan kerangka kerja yang konsisten—modul, tema, izin akses, menu, konfigurasi, dan layanan pendukung—sehingga produk aplikasi dapat dikembangkan tanpa membangun ulang fondasi dari nol.

Produk pertama yang dibangun di atas CosmicLib Engine adalah **CosmicLib Library**: Sistem Informasi Perpustakaan Sekolah yang berfokus pada kebutuhan operasional perpustakaan di satuan pendidikan.

Dokumen ini menjelaskan *apa* CosmicLib Engine, *mengapa* proyek ini ada, *siapa* yang dilayani, serta *arah* pengembangannya—tanpa masuk ke detail implementasi teknis.

---

## 2. Latar Belakang

Banyak satuan pendidikan di Indonesia—SMA, SMK, SMP, madrasah, dan yayasan—masih mengelola perpustakaan dengan cara yang kurang efisien:

- Pencatatan manual di buku besar atau spreadsheet yang rawan hilang dan sulit diaudit.
- Software legacy yang sulit diinstal, sulit diperbarui, atau sudah tidak terawat.
- Keterbatasan anggaran dan sumber daya IT di sekolah, sehingga solusi cloud berbayar atau VPS mahal sering tidak realistis.
- Kebutuhan operasional yang beragam antar sekolah, sementara banyak aplikasi bersifat “monolitik kaku” dan sulit diperluas.

Sekolah membutuhkan sistem yang:

- **Mudah diinstal** pada infrastruktur yang umum dimiliki (shared hosting cPanel).
- **Mudah digunakan** oleh pustakawan tanpa latar belakang teknis mendalam.
- **Modular**, sehingga fitur dapat ditambah atau dikurangi sesuai kebutuhan.
- **Terjangkau** dan berkelanjutan dalam jangka panjang.

CosmicLib dibuat untuk menjawab kebutuhan tersebut: membangun *engine* yang kuat dan terbuka, lalu menghadirkan produk perpustakaan sebagai langkah pertama, dengan ruang ekspansi ke domain pendidikan lainnya.

---

## 3. Tujuan

Tujuan utama proyek CosmicLib Engine:

1. Menyediakan **fondasi CMS modular** untuk aplikasi pendidikan berbasis Laravel.
2. Membangun produk pertama **CosmicLib Library** sebagai SIP yang lengkap dan relevan bagi sekolah.
3. Menurunkan hambatan teknologi bagi sekolah dengan **installer dan alur operasional yang sederhana**.
4. Mengoptimalkan pengalaman penggunaan pada **shared hosting cPanel** tanpa mengabaikan opsi VPS/Cloud.
5. Menjamin **keamanan dasar** (autentikasi, otorisasi, validasi, proteksi web) sejak tahap desain.
6. Menetapkan **dokumentasi dan blueprint** sebagai sumber kebenaran sebelum implementasi.
7. Mendorong **kode bersih, maintainable, dan dapat diuji**.
8. Mendukung **multi-peran pengguna** (admin, pustakawan, guru, siswa, dan pemangku kepentingan sekolah).
9. Menyediakan jalur ekspansi produk (akademik, arsip, inventaris, keuangan, e-learning) di atas engine yang sama.
10. Membangun ekosistem yang **ramah kontributor dan ramah AI**, agar pengembangan tetap konsisten lintas tim.
11. Menyelaraskan bahasa antarmuka dengan pengguna sekolah: **Bahasa Indonesia**.
12. Menyiapkan fondasi menuju rilis stabil yang siap diuji di lingkungan sekolah nyata.

---

## 4. Visi

Menjadi **platform CMS modular terdepan untuk aplikasi pendidikan di Indonesia**—memberdayakan setiap sekolah dengan teknologi yang modern, terjangkau, mudah digunakan, dan dapat berkembang seiring kebutuhan lembaga.

Dalam jangka panjang, CosmicLib Engine diharapkan menjadi “kerangka bersama” bagi sejumlah produk pendidikan yang saling kompatibel, bukan sekadar satu aplikasi perpustakaan berdiri sendiri.

---

## 5. Misi

1. Merancang dan memelihara **arsitektur modular** yang jelas dan terdokumentasi.
2. Mengembangkan **CosmicLib Library** sebagai SIP yang sesuai praktik operasional perpustakaan sekolah.
3. Memastikan sistem dapat dioperasikan oleh **pustakawan non-IT** dengan antarmuka yang ramah.
4. Mengoptimalkan produk untuk **resource terbatas** (khususnya shared hosting).
5. Menjaga standar **keamanan dan kualitas** pada setiap fase pengembangan.
6. Menulis dan memelihara **dokumentasi resmi** yang dapat dipahami developer, AI, dan stakeholder.
7. Menetapkan **blueprint dan kontrak desain** sebelum menulis kode aplikasi.
8. Menyediakan kemampuan **instalasi, backup, restore, dan pembaruan sistem** yang andal.
9. Membuka ruang kontribusi melalui **arsitektur terbuka** (modul, tema, plugin).
10. Memfasilitasi **pengembangan berbasis AI** dengan aturan, workflow, dan SSOT yang tegas.
11. Meluaskan manfaat engine ke **produk pendidikan lain** setelah fondasi dan produk pertama matang.
12. Menjaga arah produk tetap berorientasi pada **kebutuhan sekolah Indonesia**.

---

## 6. Core Values

| Nilai | Penjelasan |
| :--- | :--- |
| **Modular** | Fitur dipisah ke dalam modul/engine mandiri agar dapat diaktifkan, diluas, atau diganti tanpa merusak inti sistem. |
| **Clean Code** | Kode harus mudah dibaca, bermakna, dan mudah diperbaiki—bukan sekadar “berjalan”. |
| **Documentation First** | Keputusan dan kontrak didokumentasikan lebih dulu agar tim dan AI bekerja dari pemahaman yang sama. |
| **Blueprint First** | Desain (arsitektur, skema, alur) ditetapkan sebelum implementasi agar menghindari pengerjaan ulang. |
| **Security First** | Keamanan adalah syarat dasar, bukan fitur tambahan di akhir. |
| **Maintainability** | Setiap perubahan harus mudah dipelihara jangka panjang oleh orang atau AI yang berbeda. |
| **Scalability** | Struktur mendukung pertumbuhan fitur, pengguna, dan data tanpa mengorbankan kejelasan. |
| **AI Friendly** | Repository dan dokumen disusun agar AI coding assistant dapat bekerja akurat dan konsisten. |
| **Open Architecture** | Batas engine dan titik ekstensi jelas, membuka ruang modul/tema/plugin tanpa mengunci core. |
| **Developer Friendly** | Konvensi, alur kerja, dan DX yang jelas menurunkan biaya kognitif bagi kontributor baru. |

---

## 7. Produk CosmicLib

CosmicLib Engine adalah **fondasi bersama**. Di atasnya dapat dibangun berbagai produk domain. Produk pertama yang diprioritaskan adalah CosmicLib Library; produk lain adalah arah ekspansi jangka menengah–panjang.

| Produk | Deskripsi singkat |
| :--- | :--- |
| **CosmicLib Library** | Sistem Informasi Perpustakaan Sekolah: katalog, anggota, sirkulasi, denda, laporan, dan operasional harian pustakawan. |
| **CosmicLib Academic** | Aplikasi pendukung administrasi akademik (jadwal, rombel, data siswa/guru, dan proses akademik terkait). |
| **CosmicLib Archive** | Sistem arsip digital lembaga: klasifikasi, penyimpanan, penelusuran, dan retensi dokumen. |
| **CosmicLib Inventory** | Manajemen inventaris aset sekolah: pencatatan, peminjaman alat, dan pelacakan aset. |
| **CosmicLib Finance** | Modul keuangan lembaga pendidikan: pencatatan transaksi, laporan sederhana, dan alur persetujuan dasar. |
| **CosmicLib E-Learning** | Platform pembelajaran digital: materi, aktivitas belajar, dan dukungan interaksi guru–siswa. |

> **Catatan:** Pada Sprint dan fase saat ini, fokus resmi adalah **CosmicLib Engine + CosmicLib Library**. Produk lain tercantum sebagai peta produk, bukan komitmen implementasi segera.

---

## 8. Target Pengguna

### Institusi sasaran

- Sekolah SMA
- Sekolah SMK
- Sekolah SMP
- Madrasah
- Yayasan Pendidikan

### Peran pengguna

| Peran | Kebutuhan utama |
| :--- | :--- |
| **Administrator** | Konfigurasi sistem, pengguna, modul, dan keamanan. |
| **Kepala Sekolah** | Ringkasan & laporan untuk pengambilan keputusan. |
| **Kepala Perpustakaan** | Kebijakan perpustakaan, pengawasan layanan, dan kinerja unit. |
| **Pustakawan** | Operasional harian: katalog, sirkulasi, anggota, denda. |
| **Guru** | Informasi koleksi, status pinjaman terkait siswa/kelas (sesuai kebijakan). |
| **Siswa** | Pencarian koleksi, status pinjaman, dan riwayat yang relevan. |
| **Operator Sekolah** | Operasi teknis harian: data master, bantuan instalasi/pemeliharaan ringan. |
| **Yayasan** | Monitoring lintas unit / laporan agregat (sesuai ekspansi produk). |

---

## 9. Lingkup Proyek

### Yang termasuk

- Perancangan dan dokumentasi **CosmicLib Engine** (arsitektur, engine inti, standar pengembangan).
- Pengembangan produk pertama **CosmicLib Library** sesuai cetak biru.
- Installer, backup/restore, dan mekanisme pembaruan sistem.
- Panduan deployment untuk shared hosting cPanel, serta dukungan konsep untuk VPS/Cloud.
- Standar dokumentasi, blueprint, keamanan konseptual, dan pedoman AI development.
- Antarmuka pengguna dalam **Bahasa Indonesia**.

### Yang tidak termasuk (saat ini)

- Implementasi penuh seluruh peta produk (Academic, Archive, Inventory, Finance, E-Learning) sebelum Library matang.
- Fitur yang mengharuskan infrastruktur enterprise khusus tanpa jalur shared hosting.
- Integrasi pihak ketiga yang belum ditetapkan di blueprint resmi.
- Customisasi khusus satu sekolah di luar mekanisme modul/tema/plugin.

> **Catatan:** Batasan ini menjaga fokus Fase 1–produk pertama tetap jelas. Ekspansi dilakukan setelah fondasi stabil.

---

## 10. Target Platform

| Platform | Status |
| :--- | :--- |
| **Shared Hosting cPanel** | Target utama — paling relevan bagi sekolah. |
| **VPS Linux** | Didukung — untuk sekolah/yayasan dengan kontrol server sendiri. |
| **Cloud** | Didukung secara konsep — deployment pada layanan cloud umum. |
| **Docker** | **Future** — kontainerisasi resmi pada fase lanjutan. |

---

## 11. Teknologi

Ringkasan teknologi (gambaran besar, bukan spesifikasi mendalam):

| Area | Teknologi |
| :--- | :--- |
| Framework | Laravel 12 |
| Runtime | PHP |
| Database | MySQL |
| Template UI | Blade |
| CSS/UI Kit | Bootstrap 5 |
| Asset bundling | Vite |
| Kolaborasi & distribusi | GitHub |
| Bahasa antarmuka | Bahasa Indonesia |

Detail stack, versi, dan konvensi teknis tersedia di `PROJECT_MANIFEST.md` dan dokumen arsitektur.

---

## 12. Prinsip Pengembangan

| Prinsip | Makna |
| :--- | :--- |
| **Blueprint First** | Cetak biru dan kontrak desain ditetapkan sebelum kode aplikasi ditulis. |
| **Documentation First** | Dokumentasi adalah bagian dari produk, bukan lampiran belakangan. |
| **Architecture First** | Setiap fitur harus masuk dalam arsitektur Modular Monolith dan batas engine. |
| **Quality First** | Kejelasan, kebersihan, dan ketepatan lebih diutamakan daripada kecepatan ceroboh. |
| **Security First** | Ancaman umum (XSS, CSRF, injeksi, otorisasi) dipertimbangkan sejak desain. |
| **Testing First** | Verifikasi dilakukan sebelum klaim “selesai”. |
| **Deployment Last** | Perilisan dilakukan setelah kualitas, keamanan, dan dokumentasi siap. |

---

## 13. AI Development

CosmicLib dirancang agar dapat dikembangkan secara konsisten oleh manusia dan AI coding assistant (Claude, Codex, Cline, ChatGPT, AI Studio, GitHub Copilot, dan sejenisnya).

**Aturan utama:**

1. Seluruh AI **wajib membaca dokumentasi** (`AGENTS.md`, `PROJECT_MANIFEST.md`, `docs/00_SYSTEM_PROMPT.md`, dan dokumen terkait) sebelum bekerja.
2. AI **tidak boleh langsung membuat kode** tanpa memahami proyek, arsitektur, dan lingkup perubahan.
3. AI harus menyusun **rencana**, menjelaskan **file yang akan diubah**, lalu baru mengimplementasikan.
4. Jika permintaan pengguna bertentangan dengan blueprint, AI harus **menjelaskan konflik** dan menawarkan alternatif yang menjaga konsistensi.

Referensi utama perilaku AI: [`docs/00_SYSTEM_PROMPT.md`](00_SYSTEM_PROMPT.md) dan [`docs/31_AI_GUIDELINE.md`](31_AI_GUIDELINE.md).

---

## 14. Roadmap

Peta tahap pengembangan tingkat tinggi (gambaran besar). Rincian milestone dan checklist rilis ada di dokumen roadmap khusus.

| Phase | Fokus |
| :--- | :--- |
| **Phase 1** | Blueprint & dokumentasi resmi |
| **Phase 2** | Core Engine |
| **Phase 3** | Authentication |
| **Phase 4** | Theme Engine |
| **Phase 5** | Permission Engine |
| **Phase 6** | Menu Engine |
| **Phase 7** | Module Engine |
| **Phase 8** | CosmicLib Library (produk pertama) |
| **Phase 9** | Installer |
| **Phase 10** | Backup & Restore |
| **Phase 11** | System Update |
| **Phase 12** | Deployment |

> **Status saat ini:** Phase 1 — Blueprint. Kode aplikasi Laravel belum di-scaffold hingga fase blueprint ditandai selesai.

Untuk detail prioritas fitur dan kriteria rilis, lihat [`docs/29_ROADMAP.md`](29_ROADMAP.md) dan [`docs/30_RELEASE_PLAN.md`](30_RELEASE_PLAN.md).

---

## 15. Kesimpulan

CosmicLib Engine adalah fondasi modular untuk membangun aplikasi pendidikan Indonesia. Produk pertamanya, CosmicLib Library, menjawab kebutuhan nyata sistem informasi perpustakaan sekolah yang modern, terjangkau, dan dapat dijalankan pada infrastruktur yang umum dimiliki sekolah.

Arah pengembangannya jelas: **dokumentasi dan blueprint dulu**, lalu fondasi engine, lalu produk perpustakaan, lalu kemampuan operasional (installer, backup, update), dan akhirnya deployment yang matang. Dengan core values yang tegas dan prinsip pengembangan yang konsisten, CosmicLib bertujuan tumbuh secara terukur—dari satu produk perpustakaan menuju ekosistem aplikasi pendidikan di atas engine yang sama.

---

## Referensi

| Dokumen | Peran |
| :--- | :--- |
| [`00_SYSTEM_PROMPT.md`](00_SYSTEM_PROMPT.md) | Standar perilaku AI (SSOT) |
| [`02_VISION.md`](02_VISION.md) | Visi & filosofi desain (detail) |
| [`03_ARCHITECTURE.md`](03_ARCHITECTURE.md) | Arsitektur sistem |
| [`29_ROADMAP.md`](29_ROADMAP.md) | Roadmap detail |
| [`PROJECT_MANIFEST.md`](../PROJECT_MANIFEST.md) | Spesifikasi teknis proyek |
| [`AGENTS.md`](../AGENTS.md) | Aturan universal AI |
| [`README.md`](../README.md) | Pengantar repository |

## Catatan

- Dokumen ini adalah **entry point konseptual**. Untuk detail teknis, lanjut ke arsitektur dan dokumen engine terkait.
- Jangan memperlakukan peta produk masa depan sebagai spek implementasi Phase 1.

---

*CosmicLib Engine v1.0 — Sprint 1 · Prompt 004*
