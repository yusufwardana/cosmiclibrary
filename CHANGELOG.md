# 📜 Changelog (Riwayat Perubahan)

Semua perubahan penting pada proyek **CosmicLib Engine** akan didokumentasikan di berkas ini. Format riwayat ini didasarkan pada [Keep a Changelog](https://keepachangelog.com/en/1.0.0/) dan mematuhi aturan [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

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
