# AI Studio / Gemini Developer Guidelines: CosmicLib Engine

This file defines the development behavior for developers and agents working inside **Google AI Studio** or using **Gemini**.

---

## ⚠️ Mandatory Workflow

AI Studio / Gemini **WAJIB** mengikuti alur kerja ini:

1. **Membaca seluruh dokumentasi** — Baca `AGENTS.md`, `PROJECT_MANIFEST.md`, dan dokumen terkait di `docs/`.
2. **Tidak langsung coding** — Analisis project terlebih dahulu.
3. **Membuat rencana implementasi** — Jelaskan file yang akan diubah dan alasannya.
4. **Menghindari duplicate code** — Gunakan service layer pattern.
5. **Mengikuti Laravel Best Practice** — PSR-12, SOLID, thin controller.

---

## Workspace Capabilities

- **Platform Ingress**: Our development server is locked to port `3000` with host `0.0.0.0` inside a Cloud Run container.
- **Environment Handling**: Secret keys (such as `GEMINI_API_KEY`) must never be hardcoded. They should be loaded server-side or referenced dynamically.
- **Iframe Sandboxing**: Avoid APIs that break in iframes (e.g., `window.alert`, `window.open`). Render modern web overlays/dialogs instead.

---

## 🚫 Prohibitions

- ❌ Do NOT delete existing files or features
- ❌ Do NOT create duplicate code
- ❌ Do NOT hardcode roles, permissions, menus, or colors
- ❌ Do NOT write business logic in controllers
- ❌ Do NOT use `env()` outside config files
- ❌ Do NOT make random changes to files — verify they exist and review their headings first

---

## Language Policy

- **User Interface**: Bahasa Indonesia (label, pesan, notifikasi, help text)
- **Source Code**: English (classes, variables, methods, tables, columns, routes)

## How to Iterate on Documentation

- Do not make random changes to files. Always verify that they exist and review their headings.
- Create beautiful, descriptive interactive UI layers in React (in `src/`) to let users browse and view these documents directly in their browser preview.

---

## Project Focus

Currently in **Phase 1 (Blueprint & Documentation)**. Do not scaffold Laravel code until this phase is marked complete.

## Required Reading

- [`AGENTS.md`](AGENTS.md) — Universal AI rules
- [`PROJECT_MANIFEST.md`](PROJECT_MANIFEST.md) — Project specifications
- [`docs/23_CODING_STANDARD.md`](docs/23_CODING_STANDARD.md) — Coding standards
- [`docs/31_AI_GUIDELINE.md`](docs/31_AI_GUIDELINE.md) — AI development guidelines
