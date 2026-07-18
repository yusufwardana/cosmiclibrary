# CosmicLib Engine AI Development Standard

> **MASTER SYSTEM PROMPT** — Single Source of Truth (SSOT) bagi seluruh AI yang bekerja pada repository CosmicLib Engine.
>
> Seluruh AI **WAJIB** membaca dokumen ini sebelum melakukan pekerjaan apa pun.

| Atribut | Nilai |
| :--- | :--- |
| **Dokumen** | `docs/00_SYSTEM_PROMPT.md` |
| **Versi** | 1.0 |
| **Status** | `🟢 Active` — Standar resmi pengembangan AI |
| **Berlaku untuk** | Claude Code, Codex, Cline, ChatGPT, AI Studio, GitHub Copilot, dan seluruh AI coding assistant |
| **Fase proyek** | Blueprint & Documentation (Fase 1) — belum menulis kode Laravel hingga fase ini selesai |

---

## 1. PROJECT IDENTITY

| Atribut | Spesifikasi |
| :--- | :--- |
| **Nama Engine** | CosmicLib Engine |
| **Produk Pertama** | CosmicLib Library (Sistem Informasi Perpustakaan) |
| **Framework** | Laravel 12 |
| **Backend** | PHP 8.3+ |
| **Frontend** | Blade, Bootstrap 5, Vite |
| **Database** | MySQL |
| **Target Hosting** | Shared Hosting cPanel, VPS Linux, Cloud |
| **Bahasa UI** | Bahasa Indonesia |
| **Bahasa kode** | English (nama kelas, variabel, tabel, skema, API) |
| **Architecture** | Modular Monolith |

CosmicLib Engine adalah fondasi modular untuk membangun produk perangkat lunak berbasis Laravel. Produk pertama yang dibangun di atas engine ini adalah **CosmicLib Library**.

---

## 2. PROJECT PHILOSOPHY

CosmicLib Engine dibangun dengan filosofi berikut. Setiap keputusan arsitektur dan implementasi harus mencerminkan prinsip-prinsip ini.

| Prinsip | Makna |
| :--- | :--- |
| **Modular** | Fitur terisolasi dalam modul mandiri; engine inti tidak digabung dengan logika domain aplikasi. |
| **Clean Architecture** | Pemisahan tanggung jawab yang jelas antar lapisan (Presentation → Controller → Service → Repository → Data). |
| **Reusable** | Komponen, service, dan engine dirancang agar dapat digunakan ulang lintas modul dan produk. |
| **Secure** | Keamanan data dan aplikasi diutamakan pada setiap lapisan—validasi, otorisasi, dan proteksi bawaan Laravel. |
| **Maintainable** | Kode mudah dipelihara, dibaca, dan diperluas tanpa refactor besar yang tidak perlu. |
| **Scalable** | Struktur mendukung pertumbuhan fitur, pengguna, dan volume data tanpa mengorbankan kejelasan arsitektur. |
| **AI Friendly** | Repository, dokumentasi, dan naming convention disusun agar AI dapat memahami konteks dengan cepat dan akurat. |
| **Developer Friendly** | DX tinggi: alur jelas, dokumentasi lengkap, konvensi konsisten, dan tooling yang mudah diikuti. |

---

## 3. AI MISSION

Seluruh AI yang bekerja pada repository ini bertugas untuk:

1. **Menjaga kualitas project** — setiap perubahan harus menaikkan atau mempertahankan standar kualitas.
2. **Menghindari duplicate code** — reuse service, helper, dan pola yang sudah ada.
3. **Mengikuti blueprint** — rujuk `blueprint/` dan skema yang sudah ditetapkan.
4. **Mengikuti architecture** — hormati Modular Monolith, Service Layer, dan batas antar engine.
5. **Menulis dokumentasi** — setiap perubahan material diperbarui di `docs/` dan `CHANGELOG.md`.
6. **Membuat kode bersih** — readable, testable, sesuai PSR-12 dan Laravel Best Practice.

Pada Fase 1 (Blueprint & Documentation), misi AI adalah **menyempurnakan dokumentasi dan cetak biru**, bukan men-scaffold aplikasi Laravel.

---

## 4. AI WORKFLOW

Sebelum coding (atau sebelum mengubah dokumentasi secara material), AI **wajib** menjalankan alur berikut:

```
Step 1   Membaca PROJECT_MANIFEST.md
   ↓
Step 2   Membaca SYSTEM PROMPT (dokumen ini)
   ↓
Step 3   Membaca Architecture (docs/03_ARCHITECTURE.md) + dokumen terkait
   ↓
Step 4   Menganalisis project (struktur, dependensi, dampak)
   ↓
Step 5   Membuat rencana implementasi
   ↓
Step 6   Menjelaskan file yang akan diubah / dibuat
   ↓
Step 7   Implementasi
   ↓
Step 8   Testing
   ↓
Step 9   Update dokumentasi & CHANGELOG
   ↓
Step 10  Ringkasan perubahan
```

### Dokumen pendukung (wajib kontekstual)

| Prioritas | File | Kapan dibaca |
| :--- | :--- | :--- |
| 🔴 Wajib | `AGENTS.md` | Setiap sesi |
| 🔴 Wajib | `PROJECT_MANIFEST.md` | Setiap sesi |
| 🔴 Wajib | `docs/00_SYSTEM_PROMPT.md` | Setiap sesi |
| 🔴 Wajib | `docs/03_ARCHITECTURE.md` | Setiap pekerjaan teknis |
| 🟡 Kontekstual | `docs/23_CODING_STANDARD.md` | Saat menulis / mereview kode |
| 🟡 Kontekstual | `docs/06_DATABASE_DESIGN.md`, `docs/24_DATABASE_SCHEMA.md` | Saat menyentuh database |
| 🟡 Kontekstual | `docs/25_LIBRARY_MODULE.md` | Saat menyentuh modul perpustakaan |
| 🟡 Kontekstual | `docs/31_AI_GUIDELINE.md` | Panduan AI mendalam |

### Instruction file per AI

| AI Assistant | Instruction File |
| :--- | :--- |
| Claude | `CLAUDE.md` |
| Codex / GitHub Copilot | `CODEX.md` |
| ChatGPT | `prompts/chatgpt/README.md` |
| Cline | `.clinerules` |
| AI Studio / Gemini | `AI_STUDIO.md` |

---

## 5. AI DEVELOPMENT RULE

AI **WAJIB**:

- ✓ Membaca dokumentasi sebelum bertindak
- ✓ Memahami struktur folder dan batas engine
- ✓ Menggunakan Laravel Best Practice
- ✓ Menggunakan prinsip **SOLID**
- ✓ Mengikuti **PSR-12**
- ✓ Menerapkan **DRY** (Don't Repeat Yourself)
- ✓ Menerapkan **KISS** (Keep It Simple, Stupid)
- ✓ Menerapkan **YAGNI** (You Aren't Gonna Need It)
- ✓ Menggunakan **Service Layer** bila diperlukan
- ✓ Menggunakan **Repository Pattern** bila diperlukan
- ✓ Menggunakan **Form Request Validation**
- ✓ Menggunakan **Policy** untuk otorisasi
- ✓ Menggunakan **Middleware** untuk concern lintas-request
- ✓ Menggunakan **Migration** untuk setiap perubahan skema
- ✓ Menggunakan **Seeder** untuk data awal / data demo

### Aturan tambahan yang mengikat

- Controller harus tipis; logika bisnis di Service.
- Jangan gunakan `env()` di luar file `config/`.
- Raw SQL hanya jika Eloquent tidak memadai, dan selalu dengan parameter binding.
- Jangan gunakan `dd()` atau `dump()` di kode produksi.
- UI text wajib Bahasa Indonesia; identifier kode wajib English.

---

## 6. AI DILARANG

Jangan pernah:

| Larangan | Alasan |
| :--- | :--- |
| Menghapus file tanpa alasan yang jelas dan disepakati | Merusak jejak proyek dan dokumentasi |
| Menghapus fitur yang sudah ada | Melanggar stabilitas produk |
| Hardcode menu | Menu harus dari Menu Engine |
| Hardcode role | Role harus dari Permission Engine |
| Hardcode permission | Permission harus dari Permission Engine |
| Hardcode warna / token visual | Warna harus dari Theme Engine |
| Hardcode konfigurasi bisnis | Konfigurasi harus dari Setting Engine |
| Mengubah database tanpa migration | Skema harus dapat direproduksi |
| Menggunakan query mentah jika Eloquent cukup | Menurunkan maintainability & keamanan |
| Membuat duplicate code | Melanggar DRY dan mempersulit perawatan |
| Mengubah struktur proyek tanpa analisis | Merusak konsistensi arsitektur |
| Mengekspos secret (API key, password, salt) ke git | Risiko keamanan kritis |
| Menulis / scaffold kode Laravel selama Fase 1 masih aktif | Melanggar fase Blueprint & Documentation |

---

## 7. ENGINE PRINCIPLE

Semua perilaku dinamis sistem **harus** melalui engine yang tepat. AI tidak boleh melewatkan engine untuk “jalan pintas”.

| Sumber kebenaran | Engine |
| :--- | :--- |
| Semua **menu** | Menu Engine |
| Semua **permission** & role | Permission Engine |
| Semua **warna** & token visual | Theme Engine |
| Semua **konfigurasi** sistem | Setting Engine (System Setting) |
| Semua **modul** | Module Engine |
| Semua **widget** | Widget Engine |
| Semua **plugin** | Plugin Engine |

Engine pendukung lain (Media, Notification, Installer, Backup, Update, License, Core) diatur dalam dokumentasi masing-masing di `docs/07`–`docs/20`. Batas tanggung jawab antar engine harus dihormati.

---

## 8. DOCUMENTATION RULE

Setiap perubahan material harus:

1. **Memperbarui `CHANGELOG.md`** — entri yang jelas sesuai Keep a Changelog.
2. **Memperbarui dokumentasi terkait** di `docs/` (dan blueprint jika skema berubah).
3. **Memberikan komentar seperlunya** — jelaskan *mengapa*, bukan mengulangi *apa* yang sudah jelas dari kode.
4. **Menjelaskan alasan perubahan** — dalam ringkasan akhir pekerjaan kepada pengguna.

Dokumentasi tetap di `docs/`. Blueprint skema di `blueprint/`. Contoh di `examples/`. Template prompt di `prompts/`.

---

## 9. CODING STYLE

Standar gaya kode CosmicLib Engine:

| Aturan | Detail |
| :--- | :--- |
| **PSR-12** | Format PHP wajib |
| **Laravel Best Practice** | Konvensi framework Laravel 12 |
| **Clean Code** | Nama bermakna, intent jelas |
| **Readable Code** | Mudah dibaca manusia dan AI |
| **Small Function** | Fungsi pendek, fokus tunggal |
| **Single Responsibility** | Satu kelas / method = satu tanggung jawab |
| **Dependency Injection** | Hindari new-ing service di dalam constructor secara sembarangan; inject dependency |
| **Type Hint** | Parameter bertipe |
| **Return Type** | Return type eksplisit |
| **Strict Typing** | `declare(strict_types=1);` bila memungkinkan (wajib pada service baru) |

### Naming conventions

| Jenis | Convention | Contoh |
| :--- | :--- | :--- |
| Class / Controller | PascalCase | `BookCirculationService` |
| Method / variable | camelCase | `borrowBook()`, `$memberId` |
| Table / column | snake_case | `borrow_records`, `return_date` |
| Route name | kebab-case + dot | `admin.books.index` |

---

## 10. DATABASE RULE

Semua perubahan database harus melalui:

1. **Migration** — satu-satunya cara mengubah skema.
2. **Seeder** — data awal, referensi, atau demo.
3. **Factory** — bila diperlukan untuk testing / seeding massal.

**Tidak boleh** mengedit database secara manual (phpMyAdmin, SQL ad-hoc di production) sebagai bagian dari alur pengembangan.

Gunakan eager loading untuk mencegah N+1. Indeks wajib pada kolom yang sering di-query (lihat blueprint untuk tabel volume tinggi seperti `borrow_records`, `books`).

---

## 11. UI RULE

| Aturan | Keterangan |
| :--- | :--- |
| **Bahasa Indonesia** | Semua teks antarmuka pengguna |
| **Bootstrap 5** | Framework UI utama aplikasi (produk Laravel) |
| **Responsive** | Layar mobile hingga desktop |
| **Accessibility** | Semantik HTML, label, kontras, keyboard-friendly |
| **Dark Mode Ready** | Struktur markup/CSS siap dark mode |
| **Theme Engine** | Warna, token, dan branding dari Theme Engine—bukan hardcode |

Catatan: repository viewer (React) mengikuti identitas visual tersendiri di `AGENTS.md`; aplikasi produk CosmicLib Library tetap Blade + Bootstrap 5.

---

## 12. SECURITY

Setiap implementasi harus mempertimbangkan:

| Kontrol | Tanggung jawab |
| :--- | :--- |
| **CSRF Protection** | Form web & state-changing request |
| **XSS Protection** | Escape output Blade; sanitasi input |
| **SQL Injection Protection** | Eloquent / query builder / binding |
| **Authorization Policy** | Policy + Permission Engine |
| **Authentication** | Guard Laravel yang sesuai |
| **Rate Limiting** | Endpoint sensitif & autentikasi |
| **Validation** | Form Request / Validator sebelum logika bisnis |

Detail lengkap: `docs/22_SECURITY_GUIDELINE.md`.

---

## 13. GIT RULE

- Commit **kecil** dan terfokus.
- Pesan commit **jelas** dan menjelaskan *mengapa*.
- Gunakan **Conventional Commits**.

| Prefix | Penggunaan |
| :--- | :--- |
| `feat:` | Fitur baru |
| `fix:` | Perbaikan bug |
| `docs:` | Pembaruan dokumentasi |
| `refactor:` | Perubahan struktur tanpa mengubah perilaku |
| `test:` | Penambahan / perbaikan tes |
| `style:` | Format, whitespace, tanpa perubahan logika |
| `chore:` | Maintenance, dependency, tooling |

AI **tidak** membuat commit kecuali diminta secara eksplisit oleh pengguna.

---

## 14. RESPONSE FORMAT

### Sebelum coding / implementasi

AI harus menjelaskan:

1. **Analisis** — pemahaman masalah dan konteks
2. **File yang akan diubah** — daftar eksplisit
3. **Alasan** — mengapa perubahan itu diperlukan
4. **Risiko** — dampak samping, breaking change, ketidakcocokan arsitektur
5. **Rencana implementasi** — langkah berurutan

### Setelah selesai

AI harus menyampaikan:

1. **Ringkasan** — apa yang berubah dan mengapa
2. **Daftar file** — file yang dibuat / diubah
3. **Testing** — apa yang diuji atau bagaimana menguji
4. **Dokumentasi** — dokumen mana yang diperbarui

---

## 15. QUALITY CHECKLIST

Sebelum menyelesaikan pekerjaan, AI **wajib** memastikan:

- [ ] Tidak ada duplicate code
- [ ] Tidak ada hardcode (menu, role, permission, warna, konfigurasi)
- [ ] Dokumentasi diperbarui (`docs/` + `CHANGELOG.md` bila material)
- [ ] Mengikuti Blueprint
- [ ] Mengikuti Architecture
- [ ] Mengikuti Coding Standard (`docs/23_CODING_STANDARD.md`)
- [ ] Mengikuti Laravel Best Practice
- [ ] Mengikuti Engine Principle (bagian 7)
- [ ] Tidak melanggar larangan Fase 1 (jika masih Blueprint)

---

## 16. COSMICLIB PRINCIPLE

Urutan prioritas yang tidak boleh dibalik:

1. **Blueprint First** — desain sebelum implementasi.
2. **Documentation First** — dokumentasikan keputusan dan kontrak.
3. **Architecture First** — hormati Modular Monolith dan batas engine.
4. **Quality First** — kualitas di atas kecepatan kilat yang ceroboh.
5. **Security First** — keamanan bukan afterthought.
6. **Testing First** — verifikasi sebelum mengklaim selesai.
7. **Deployment Last** — rilis setelah kualitas, keamanan, dan dokumentasi siap.

---

## 17. FINAL STATEMENT

Seluruh AI yang bekerja pada repository CosmicLib **wajib** mengikuti dokumen ini.

Jika terdapat **konflik** antara permintaan pengguna dengan blueprint / arsitektur proyek, AI harus:

1. Menjelaskan konflik tersebut secara eksplisit.
2. Menawarkan alternatif yang tetap menjaga konsistensi arsitektur.
3. Tidak diam-diam melanggar SSOT demi memenuhi permintaan yang bertentangan.

Dokumen ini merupakan **standar resmi pengembangan CosmicLib Engine** dan menjadi acuan hierarki tertinggi bagi perilaku AI di repository ini (bersama `AGENTS.md` dan `PROJECT_MANIFEST.md`).

---

## Referensi

| Dokumen | Peran |
| :--- | :--- |
| [`AGENTS.md`](../AGENTS.md) | Aturan universal semua AI |
| [`PROJECT_MANIFEST.md`](../PROJECT_MANIFEST.md) | Spesifikasi teknis proyek |
| [`docs/03_ARCHITECTURE.md`](03_ARCHITECTURE.md) | Arsitektur sistem |
| [`docs/23_CODING_STANDARD.md`](23_CODING_STANDARD.md) | Standar penulisan kode |
| [`docs/22_SECURITY_GUIDELINE.md`](22_SECURITY_GUIDELINE.md) | Panduan keamanan |
| [`docs/31_AI_GUIDELINE.md`](31_AI_GUIDELINE.md) | Panduan AI mendalam |
| [`CHANGELOG.md`](../CHANGELOG.md) | Riwayat perubahan |

---

*CosmicLib Engine v1.0 — Sprint 1 · Prompt 003*
