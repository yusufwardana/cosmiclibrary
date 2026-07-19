# 📜 Changelog (Riwayat Perubahan)

Semua perubahan penting pada proyek **CosmicLib Engine** akan didokumentasikan di berkas ini. Format riwayat ini didasarkan pada [Keep a Changelog](https://keepachangelog.com/en/1.0.0/) dan mematuhi aturan [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [1.1.0] - 2026-07-19

### Ditambahkan (Added)
- `docs/27_SECURITY_GUIDELINE.md` hingga `docs/38_PROMPT_LIBRARY.md` — 12 file canonical dengan penomoran baru
- `docs/50_BLUEPRINT_FINAL.md` — Blueprint Final, checklist kelengkapan dokumentasi dan status kesiapan Phase 2
- `scripts/copy_docs.ps1` — Script untuk renumbering dokumen

### Diubah (Changed)
- `docs/00_DOCS_INDEX.md` — Status tabel diperbarui: 12 file 🔄 Renumber → ✅, 50_BLUEPRINT_FINAL ⬜ Planned → ✅
- `CHANGELOG.md` — Entri v1.1.0 ditambahkan

---

## [1.1.0-alpha.1] - 2026-07-19

### Ditambahkan (Added)
- `docs/00_DOCS_INDEX.md` — Index & audit penomoran seluruh dokumen, termasuk peta collision resolution
- `blueprint/database_schema.sql` di-rewrite total (v1.1.0): 27 tabel, mencakup seluruh engine

### Diubah (Changed)
- **`blueprint/database_schema.sql`** — Sebelumnya hanya 5 tabel library (books, book_items, members, borrow_records, fines). Sekarang mencakup:
  - Users & RBAC (users, roles, permissions, role_permissions, user_roles)
  - Platform engines (modules, menus, themes, settings, media, notifications, widgets, plugins, backups)
  - Audit & activity logs (audit_logs, activity_logs)
  - Library module (categories, books, book_items, members, borrow_records, reservations, fines)
  - Laravel infrastructure (sessions, password_reset_tokens, jobs, failed_jobs, cache, cache_locks)
  - Soft deletes pada users, members, books, book_items, media
  - Indices pada FK columns dan high-cardinality filter columns
  - FULLTEXT index pada books (title, author, description)

### Diperbaiki (Fixed)
- Dokumentasi numbering collision: 11 prefix bentrok (16–26) di-audit dan canonical numbering ditetapkan di `00_DOCS_INDEX.md`

---

## [1.0.0-alpha.2] - 2026-07-16

### Ditambahkan (Added)
- 12 dokumen arsitektur baru di `docs/` (nomor 15, 20–32):
  - `15_NOTIFICATION_ENGINE.md` — Sistem notifikasi (email, WhatsApp, in-app)
  - `20_LICENSE_ENGINE.md` — Sistem lisensi dan aktivasi
  - `21_API_GUIDELINE.md` — Panduan API RESTful
  - `22_SECURITY_GUIDELINE.md` — Panduan keamanan aplikasi
  - `23_CODING_STANDARD.md` — Standar penulisan kode
  - `24_DATABASE_SCHEMA.md` — Skema database detail
  - `25_LIBRARY_MODULE.md` — Spesifikasi modul perpustakaan
  - `26_UI_GUIDELINE.md` — Panduan desain antarmuka
  - `28_CPANEL_DEPLOYMENT.md` — Panduan deployment cPanel
  - `30_RELEASE_PLAN.md` — Rencana rilis
  - `31_AI_GUIDELINE.md` — Panduan pengembangan dengan AI
  - `32_PROMPT_LIBRARY.md` — Koleksi template prompt
- File konfigurasi root baru: `.editorconfig`, `.gitattributes`
- Direktori `prompts/chatgpt/` dengan README.md

### Diubah (Changed)
- `docs/00_SYSTEM_PROMPT.md` diperluas menjadi Master System Prompt SSOT v1.0 (Sprint 1 · Prompt 003): identitas proyek, filosofi, misi AI, workflow 10 langkah, aturan & larangan, engine principle, coding/DB/UI/security/git rules, response format, quality checklist, dan CosmicLib Principle.
- `docs/01_PROJECT_OVERVIEW.md` diperluas menjadi Project Overview resmi v1.0 (Sprint 1 · Prompt 004): pendahuluan, latar belakang, tujuan, visi/misi, core values, peta produk, target pengguna, lingkup, platform, teknologi, prinsip pengembangan, AI development, roadmap 12 fase, dan kesimpulan.
- `docs/02_VISION.md` diperluas menjadi Vision resmi v1.0 (Sprint 1 · Prompt 005): visi utama, misi, core values, North Star, future ecosystem, design philosophy, AI vision, long-term goals (1–10 tahun), success indicator, komitmen proyek, dan penutup.
- `docs/03_ARCHITECTURE.md` diperluas menjadi spesifikasi arsitektur utama v1.0 (Sprint 1 · Prompt 006): principles, overview, layers, Core & engine ecosystem, module/theme/permission/menu/plugin/widget/setting architecture, data flow, dependency rule, security, deployment, AI architecture, future architecture, ADR ringkas, dan checklist.
- Restrukturisasi penomoran dokumen `docs/` dari 21 file (00–20) menjadi 33 file (00–32):
  - `15_SYSTEM_SETTING.md` → `16_SETTING_ENGINE.md`
  - `16_INSTALLER.md` → `17_INSTALLER_ENGINE.md`
  - `17_SYSTEM_UPDATE.md` → `19_UPDATE_ENGINE.md`
  - `18_BACKUP_RESTORE.md` → `18_BACKUP_ENGINE.md`
  - `19_DEPLOYMENT.md` → `27_DEPLOYMENT.md`
  - `20_ROADMAP.md` → `29_ROADMAP.md`
- Seluruh 33 dokumen `docs/` diperkaya dengan template standar (Deskripsi, Tujuan, Ruang Lingkup, Table of Contents, Status, Kerangka Sistem, Referensi, Catatan).
- `README.md` diperkaya dengan section: Visi, Misi, Filosofi, Target, Fitur, Teknologi, Arsitektur, Dokumentasi, Kontribusi.
- `PROJECT_MANIFEST.md` ditambahkan field: Node.js, Version, Architecture, Status, Engine reference table.
- `CLAUDE.md` ditambahkan mandatory workflow dan required reading.
- `CODEX.md` ditambahkan mandatory workflow dan prohibitions.
- `AI_STUDIO.md` ditambahkan mandatory workflow dan language policy.
- `AGENTS.md` ditambahkan universal workflow, prohibitions, dan AI-specific file references.
- `.clinerules` ditambahkan aturan lengkap engine rules, no hardcoding, SOLID, PSR-12.
- `.github/` files diperkaya sesuai standar open source profesional.

---

## [1.0.0-alpha.1] - 2026-07-12

### Ditambahkan (Added)
- Struktur direktori standar repository (`.github/`, `docs/`, `blueprint/`, `prompts/`, `examples/`, `assets/`, `scripts/`, `tests/`).
- Kerangka panduan kontribusi, kode etik, keamanan, dan template isu/pull request pada folder `.github/`.
- Cetak biru 21 dokumen awal arsitektur sistem (`00_SYSTEM_PROMPT.md` hingga `20_ROADMAP.md`) pada folder `docs/`.
- Berkas deklaratif proyek di root directory:
  - `README.md` (Deskripsi umum, filosofi, struktur, dan peta jalan).
  - `PROJECT_MANIFEST.md` (Spesifikasi teknis, standar kode, dan aturan AI).
  - `ROADMAP.md` (Detail pengerjaan jangka pendek, menengah, dan panjang).
  - `LICENSE` (Lisensi open source MIT).
  - Panduan pengembangan menggunakan asisten AI (`CLAUDE.md`, `AGENTS.md`, `CODEX.md`, `AI_STUDIO.md`, `.clinerules`).
- Antarmuka visual web interaktif berbasis React 19 + Vite + Tailwind CSS + Framer Motion sebagai dasbor peninjau repository dan pembaca dokumentasi (repository viewer).
