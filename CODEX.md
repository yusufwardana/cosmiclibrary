# Codex Guidelines: CosmicLib Engine

This file instructs **Codex / GitHub Copilot** on how to complete code inline for this project.

---

## ⚠️ Mandatory Workflow

Codex **WAJIB** mengikuti alur kerja ini:

1. **Membaca seluruh dokumentasi** — Baca `AGENTS.md`, `PROJECT_MANIFEST.md`, dan dokumen terkait di `docs/`.
2. **Tidak langsung coding** — Analisis project terlebih dahulu.
3. **Membuat rencana implementasi** — Jelaskan file yang akan diubah dan alasannya.
4. **Menghindari duplicate code** — Gunakan service layer pattern.
5. **Mengikuti Laravel Best Practice** — PSR-12, SOLID, thin controller.

---

## Key Directives

1. **Inline Documentation**: Always write DocBlocks in standard PHP DocBlock format:
   ```php
   /**
    * Melakukan transaksi peminjaman buku oleh anggota.
    *
    * @param int $memberId
    * @param array $bookIds
    * @return \CosmicLib\Core\Models\BorrowRecord
    * @throws \CosmicLib\Core\Exceptions\CirculationException
    */
   ```

2. **Language Split**:
   - Write UI and user-facing messages in **Indonesian**.
   - Write all code constructs (classes, properties, functions, databases) in **English**.

3. **Service Layer Resolution**:
   - Do not perform heavy queries or logic inside Controllers.
   - Resolve logic from services via dependency injection:
   ```php
   public function store(BorrowRequest $request, CirculationService $circulation)
   {
       $circulation->processBorrow($request->validated());
   }
   ```

---

## 🚫 Prohibitions

- ❌ Do NOT delete existing files or features
- ❌ Do NOT create duplicate code
- ❌ Do NOT hardcode roles, permissions, menus, or colors
- ❌ Do NOT write business logic in controllers
- ❌ Do NOT use `env()` outside config files

---

## Project Focus

Currently in **Phase 1 (Blueprint & Documentation)**. Do not scaffold Laravel code until this phase is marked complete.

## Required Reading

- [`AGENTS.md`](AGENTS.md) — Universal AI rules
- [`PROJECT_MANIFEST.md`](PROJECT_MANIFEST.md) — Project specifications
- [`docs/23_CODING_STANDARD.md`](docs/23_CODING_STANDARD.md) — Coding standards
- [`docs/31_AI_GUIDELINE.md`](docs/31_AI_GUIDELINE.md) — AI development guidelines
