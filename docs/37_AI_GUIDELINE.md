# ðŸŒŒ 37 — AI Guideline

## Deskripsi

Dokumen ini menyusun panduan lengkap pengembangan CosmicLib Engine menggunakan **AI Coding Assistant**, mencakup aturan umum, workflow pengembangan berbasis AI, dan batasan-batasan yang harus dipatuhi.

## Tujuan

Memastikan semua AI coding assistant (Claude, Codex, ChatGPT, Cline, AI Studio, Gemini) menghasilkan kode dan dokumentasi yang konsisten, berkualitas tinggi, dan sesuai dengan arsitektur CosmicLib Engine.

## Ruang Lingkup

- Aturan universal untuk semua AI assistant
- Workflow pengembangan berbasis AI
- File-file yang wajib dibaca sebelum coding
- Batasan dan larangan
- Panduan interaksi dengan AI
- Checklist quality assurance AI-generated code

---

## 🗂️ Table of Contents

1. [Aturan Universal AI](#aturan-universal-ai)
2. [Workflow Pengembangan](#workflow-pengembangan)
3. [Dokumen Wajib Baca](#dokumen-wajib-baca)
4. [Batasan & Larangan](#batasan--larangan)
5. [Panduan Interaksi](#panduan-interaksi)
6. [QA Checklist untuk Kode AI](#qa-checklist-untuk-kode-ai)

---

## Status

`🟡 Blueprint` — Dokumen dalam tahap perancangan.

---

## ⚙️ Kerangka Sistem

### Aturan Universal AI

*Setiap AI coding assistant yang bekerja pada CosmicLib Engine WAJIB:*

1. **Membaca seluruh dokumentasi** di `docs/` sebelum menulis kode
2. **Tidak langsung coding** — analisis dulu, buat rencana, lalu implementasi
3. **Membuat rencana implementasi** sebelum menulis kode apapun
4. **Menjelaskan file yang akan diubah** beserta alasannya
5. **Menghindari duplicate code** — gunakan service layer pattern
6. **Mengikuti Laravel Best Practice** dan PSR-12
7. **UI dalam Bahasa Indonesia**, kode dalam Bahasa Inggris

### Workflow Pengembangan

*Placeholder: Alur kerja yang harus diikuti AI:*
1. Baca brief/instruksi dari pengguna
2. Review dokumentasi terkait di `docs/` dan `blueprint/`
3. Buat rencana implementasi (file apa, perubahan apa, alasan apa)
4. Minta persetujuan pengguna sebelum mengeksekusi
5. Implementasi sesuai rencana
6. Verifikasi hasil (test, review)
7. Dokumentasikan perubahan

### Dokumen Wajib Baca

*AI harus membaca file-file ini sebelum memulai sesi pengembangan:*

| Prioritas | File | Konten |
|:---|:---|:---|
| 🔴 Wajib | `AGENTS.md` | Aturan umum semua AI |
| 🔴 Wajib | `PROJECT_MANIFEST.md` | Spesifikasi teknis proyek |
| 🔴 Wajib | `docs/03_ARCHITECTURE.md` | Arsitektur sistem |
| 🔴 Wajib | `docs/23_CODING_STANDARD.md` | Standar penulisan kode |
| 🟡 Kontekstual | `docs/06_DATABASE_DESIGN.md` | Jika bekerja dengan database |
| 🟡 Kontekstual | `docs/25_LIBRARY_MODULE.md` | Jika bekerja dengan modul perpustakaan |
| 🟡 Kontekstual | `blueprint/` | Jika bekerja dengan skema database |

### Batasan & Larangan

*Placeholder:*
- ❌ Tidak boleh menghapus file yang sudah ada
- ❌ Tidak boleh menghapus fitur yang sudah ada
- ❌ Tidak boleh membuat duplicate code
- ❌ Tidak boleh hardcode role, permission, menu, warna
- ❌ Tidak boleh menggunakan `env()` di luar file config
- ❌ Tidak boleh menulis logika bisnis di controller
- ❌ Tidak boleh membuat raw SQL query tanpa parameter binding

### Panduan Interaksi

*Placeholder: Tips untuk pengguna yang bekerja dengan AI:*
- Berikan konteks yang jelas dan spesifik
- Minta AI untuk menjelaskan rencana sebelum coding
- Review setiap output AI sebelum di-commit
- Gunakan prompt dari `prompts/` untuk setiap AI assistant

### QA Checklist untuk Kode AI

*Placeholder: Checklist review sebelum menerima kode dari AI:*
- [ ] Kode mengikuti PSR-12 dan SOLID
- [ ] Tidak ada duplicate code
- [ ] Tidak ada hardcoded values
- [ ] Semua teks UI dalam Bahasa Indonesia
- [ ] Semua variabel/kelas dalam Bahasa Inggris
- [ ] Ada error handling (try-catch)
- [ ] Ada logging untuk operasi penting
- [ ] Kode sudah di-test

---

## Referensi

- [AGENTS.md](../AGENTS.md)
- [CLAUDE.md](../CLAUDE.md)
- [CODEX.md](../CODEX.md)
- [AI_STUDIO.md](../AI_STUDIO.md)
- [23_CODING_STANDARD.md](23_CODING_STANDARD.md)

## Catatan

- Dokumen ini bersifat universal — berlaku untuk semua AI coding assistant.
- Panduan spesifik per AI tersedia di file root masing-masing (`CLAUDE.md`, `CODEX.md`, dll).
- Prompt template untuk setiap AI tersedia di direktori `prompts/`.
