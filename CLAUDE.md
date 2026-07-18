# Claude Guidelines for CosmicLib Engine

This file guides **Claude AI** on how to interact with this repository and maintain the design architecture of **CosmicLib Engine**.

---

## ⚠️ Mandatory Workflow

Claude **WAJIB** mengikuti alur kerja ini sebelum menulis kode apapun:

1. **Membaca seluruh dokumentasi** — Baca `AGENTS.md`, `PROJECT_MANIFEST.md`, dan dokumen terkait di `docs/` sebelum memulai.
2. **Tidak langsung coding** — Jangan menghasilkan kode sebelum memahami konteks penuh.
3. **Analisis project terlebih dahulu** — Pahami arsitektur, skema database, dan coding standard.
4. **Membuat rencana implementasi** — Buat dan jelaskan rencana sebelum mengeksekusi.
5. **Menjelaskan file yang akan diubah** — Sebutkan file apa yang akan dibuat/diubah dan alasannya.
6. **Menghindari duplicate code** — Gunakan service layer pattern, reuse existing code.
7. **Mengikuti Laravel Best Practice** — PSR-12, SOLID, thin controller, form request validation.

---

## 📋 Required Reading

Sebelum memulai sesi pengembangan, baca file-file ini:

| Prioritas | File | Konten |
| :--- | :--- | :--- |
| 🔴 Wajib | `AGENTS.md` | Aturan universal semua AI |
| 🔴 Wajib | `PROJECT_MANIFEST.md` | Spesifikasi teknis proyek |
| 🔴 Wajib | `docs/03_ARCHITECTURE.md` | Arsitektur sistem |
| 🔴 Wajib | `docs/23_CODING_STANDARD.md` | Standar penulisan kode |
| 🟡 Kontekstual | `docs/06_DATABASE_DESIGN.md` | Jika bekerja dengan database |
| 🟡 Kontekstual | `docs/25_LIBRARY_MODULE.md` | Jika bekerja dengan modul perpustakaan |

---

## 🛑 Larangan

- ❌ Jangan menghapus file atau fitur yang sudah ada
- ❌ Jangan membuat duplicate code
- ❌ Jangan hardcode role, permission, menu, atau warna
- ❌ Jangan menulis logika bisnis di controller
- ❌ Jangan menggunakan `env()` di luar file config
- ❌ Jangan membuat raw SQL tanpa parameter binding

---

## Development Commands

When we begin the development phase (Fase 2+), use the following standard commands:
- **Composer Dependencies**: `composer install` or `composer update`
- **Artisan Commands**:
  - Run local dev server: `php artisan serve`
  - Run database migrations: `php artisan migrate`
  - Seed database: `php artisan db:seed`
  - Run automated tests: `php artisan test`
- **Vite Asset Compiler**:
  - Install npm packages: `npm install`
  - Run compiler in watch mode: `npm run dev`
  - Build assets for production: `npm run build`

## Code Style Guidelines

- **PHP Styling**: Follow PSR-12 formatting strictly.
- **Strict Typing**: Declare `declare(strict_types=1);` at the top of newly created service classes.
- **Naming Conventions**:
  - Classes and Controllers: PascalCase (e.g., `BookCirculationService`, `MemberController`).
  - Methods and Variables: camelCase (e.g., `borrowBook()`, `$memberId`).
  - Database Tables and Columns: snake_case (e.g., `borrow_records`, `return_date`).
  - Route names: kebab-case with dots as resource delimiters (e.g., `admin.books.index`).
- **Language Policy**:
  - UI text: Bahasa Indonesia
  - Code constructs: English

## Project Focus

Currently, we are in the **Blueprint & Documentation Phase (Fase 1)**. Do not scaffold or implement actual Laravel code until this phase is marked complete. Maintain clear, structured documentation inside the `docs/` folder.
