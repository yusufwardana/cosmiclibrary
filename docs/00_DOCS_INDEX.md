# 📚 CosmicLib Engine — Dokumentasi Index & Audit

> **Peta navigasi** seluruh dokumen di `docs/`.
>
> Versi: 1.0.0 | Updated: 2026-07-19

---

## Canonical Numbering

Berikut adalah penomoran resmi dokumen CosmicLib Engine.

### Core Documentation (00–06)

| # | File | Judul | Status |
|:--|:-----|:------|:-------|
| 00 | `00_SYSTEM_PROMPT.md` | System Prompt | ✅ |
| 00 | `00_DOCS_INDEX.md` | Dokumentasi Index (file ini) | ✅ |
| 01 | `01_PROJECT_OVERVIEW.md` | Project Overview | ✅ |
| 02 | `02_VISION.md` | Vision | ✅ |
| 03 | `03_ARCHITECTURE.md` | Architecture | ✅ |
| 04 | `04_TECH_STACK.md` | Tech Stack | ✅ |
| 05 | `05_FOLDER_STRUCTURE.md` | Folder Structure | ✅ |
| 06 | `06_DATABASE_DESIGN.md` | Database Design | ✅ |

### Engine Documentation (07–26)

| # | File | Judul | Status |
|:--|:-----|:------|:-------|
| 07 | `07_CORE_ENGINE.md` | Core Engine | ✅ |
| 08 | `08_MODULE_ENGINE.md` | Module Engine | ✅ |
| 09 | `09_THEME_ENGINE.md` | Theme Engine | ✅ |
| 10 | `10_PERMISSION_ENGINE.md` | Permission Engine | ✅ |
| 11 | `11_MENU_ENGINE.md` | Menu Engine | ✅ |
| 12 | `12_WIDGET_ENGINE.md` | Widget Engine | ✅ |
| 13 | `13_PLUGIN_ENGINE.md` | Plugin Engine | ✅ |
| 14 | `14_MEDIA_ENGINE.md` | Media Engine | ✅ |
| 15 | `15_NOTIFICATION_ENGINE.md` | Notification Engine | ✅ |
| 16 | `16_SETTING_ENGINE.md` | Setting Engine | ✅ |
| 17 | `17_AUTH_ENGINE.md` | Auth Engine | ✅ |
| 18 | `18_USER_ENGINE.md` | User Engine | ✅ |
| 19 | `19_INSTALLER_ENGINE.md` | Installer Engine | ✅ |
| 20 | `20_UPDATE_ENGINE.md` | Update Engine | ✅ |
| 21 | `21_BACKUP_ENGINE.md` | Backup Engine | ✅ |
| 22 | `22_LICENSE_ENGINE.md` | License Engine | ✅ |
| 23 | `23_API_ENGINE.md` | API Engine | ✅ |
| 24 | `24_SEARCH_ENGINE.md` | Search Engine | ✅ |
| 25 | `25_LOG_ENGINE.md` | Log Engine | ✅ |
| 26 | `26_QUEUE_ENGINE.md` | Queue Engine | ✅ |

### Guidelines & Standards (27–32)

| # | File | Judul | Status |
|:--|:-----|:------|:-------|
| 27 | `27_SECURITY_GUIDELINE.md` | Security Guideline | ✅ |
| 28 | `28_CODING_STANDARD.md` | Coding Standard | ✅ |
| 29 | `29_API_GUIDELINE.md` | API Guideline | ✅ |
| 30 | `30_UI_GUIDELINE.md` | UI Guideline | ✅ |
| 31 | `31_DATABASE_SCHEMA.md` | Database Schema Reference | ✅ |
| 32 | `32_LIBRARY_MODULE.md` | Library Module Spec | ✅ |

### Operations (33–37)

| # | File | Judul | Status |
|:--|:-----|:------|:-------|
| 33 | `33_DEPLOYMENT.md` | Deployment | ✅ |
| 34 | `34_CPANEL_DEPLOYMENT.md` | cPanel Deployment | ✅ |
| 35 | `35_ROADMAP.md` | Roadmap | ✅ |
| 36 | `36_RELEASE_PLAN.md` | Release Plan | ✅ |

### AI & Prompts (37–39)

| # | File | Judul | Status |
|:--|:-----|:------|:-------|
| 37 | `37_AI_GUIDELINE.md` | AI Guideline | ✅ |
| 38 | `38_PROMPT_LIBRARY.md` | Prompt Library | ✅ |

### Final Blueprint

| # | File | Judul | Status |
|:--|:-----|:------|:-------|
| 50 | `50_BLUEPRINT_FINAL.md` | Blueprint Final | ✅ |

---

## ⚠️ Numbering Collision Audit

File-file berikut memiliki **konflik penomoran** (dua file berbagi prefix yang sama). Kolom "Canonical" menunjukkan file mana yang menjadi versi resmi pada penomoran baru di atas.

| Prefix Lama | File Lama (Duplikat) | Canonical Baru | Aksi |
|:------------|:---------------------|:----------------|:-----|
| 16 | `16_SYSTEM_SETTING_ENGINE.md` | Merged → `16_SETTING_ENGINE.md` | Konsolidasi konten |
| 17 | `17_INSTALLER_ENGINE.md` | Moved → `19_INSTALLER_ENGINE.md` | Sudah ada di 19 |
| 18 | `18_BACKUP_ENGINE.md` | Moved → `21_BACKUP_ENGINE.md` | Sudah ada di 21 |
| 19 | `19_UPDATE_ENGINE.md` | Kept → `20_UPDATE_ENGINE.md` | Sudah ada di 20 |
| 20 | `20_LICENSE_ENGINE.md` | Kept → `22_LICENSE_ENGINE.md` | Sudah ada di 22 |
| 21 | `21_API_GUIDELINE.md` | Moved → `29_API_GUIDELINE.md` | Guideline, bukan engine |
| 22 | `22_SECURITY_GUIDELINE.md` | Moved → `27_SECURITY_GUIDELINE.md` | Guideline, bukan engine |
| 23 | `23_CODING_STANDARD.md` | Moved → `28_CODING_STANDARD.md` | Standard, bukan engine |
| 24 | `24_DATABASE_SCHEMA.md` | Moved → `31_DATABASE_SCHEMA.md` | Reference doc |
| 25 | `25_LIBRARY_MODULE.md` | Moved → `32_LIBRARY_MODULE.md` | Module spec |
| 26 | `26_UI_GUIDELINE.md` | Moved → `30_UI_GUIDELINE.md` | Guideline |
| 27 | `27_DEPLOYMENT.md` | Moved → `33_DEPLOYMENT.md` | Operations |
| 28 | `28_CPANEL_DEPLOYMENT.md` | Moved → `34_CPANEL_DEPLOYMENT.md` | Operations |
| 29 | `29_ROADMAP.md` | Moved → `35_ROADMAP.md` | Operations |
| 30 | `30_RELEASE_PLAN.md` | Moved → `36_RELEASE_PLAN.md` | Operations |
| 31 | `31_AI_GUIDELINE.md` | Moved → `37_AI_GUIDELINE.md` | AI section |
| 32 | `32_PROMPT_LIBRARY.md` | Moved → `38_PROMPT_LIBRARY.md` | AI section |

### Prinsip Penomoran

```
00-06  : Core Documentation (overview, architecture, tech stack, database)
07-26  : Engine Documentation (satu nomor per engine)
27-32  : Guidelines & Standards (security, coding, API, UI, DB schema, module spec)
33-36  : Operations (deployment, roadmap, release)
37-38  : AI Documentation (guideline, prompts)
50     : Blueprint Final
```

### Catatan Penting

1. File lama **TIDAK dihapus** (sesuai aturan Phase 1). File baru dengan nomor canonical dibuat sebagai versi resmi.
2. File lama tetap ada sebagai referensi historis sampai fase konsolidasi.
3. Semua cross-reference di `03_ARCHITECTURE.md` dan `PROJECT_MANIFEST.md` mengacu ke canonical numbering.
4. Engine docs (07-26) dikelompokkan berdasarkan urutan dependency: Core → Platform → Domain-support → Lifecycle.

---

## Referensi

- [`PROJECT_MANIFEST.md`](../PROJECT_MANIFEST.md) — Manifest proyek
- [`03_ARCHITECTURE.md`](03_ARCHITECTURE.md) — Arsitektur sistem
- [`AGENTS.md`](../AGENTS.md) — Instruksi AI

---

*CosmicLib Engine v1.1 — Documentation Index*