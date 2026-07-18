# 🤖 ChatGPT Prompts — CosmicLib Engine

## Deskripsi

Direktori ini berisi koleksi prompt dan instruksi khusus untuk pengembangan CosmicLib Engine menggunakan **ChatGPT** (OpenAI GPT-4 / GPT-4o / GPT-4.5).

## Tujuan

Memastikan ChatGPT memahami konteks, arsitektur, dan aturan pengembangan CosmicLib Engine sebelum menghasilkan kode atau dokumentasi.

---

## Panduan Penggunaan ChatGPT

### Sebelum Memulai Sesi

1. **Unggah konteks proyek**: Kirimkan file `PROJECT_MANIFEST.md` dan `AGENTS.md` ke ChatGPT di awal sesi.
2. **Tentukan peran**: Minta ChatGPT berperan sebagai "Senior Laravel 12 Architect yang memahami arsitektur modular CosmicLib Engine".
3. **Referensi dokumentasi**: Arahkan ChatGPT untuk merujuk ke file-file di `docs/` sebelum menghasilkan kode.

### Aturan Wajib

- **Jangan langsung coding** — Analisis terlebih dahulu, buat rencana, jelaskan file yang akan diubah.
- **Bahasa UI**: Semua teks antarmuka pengguna wajib dalam **Bahasa Indonesia**.
- **Bahasa Kode**: Semua variabel, class, method, tabel database dalam **Bahasa Inggris**.
- **Ikuti PSR-12** dan **SOLID principles**.
- **Hindari duplicate code** — Gunakan service layer pattern.
- **Jangan hardcode**: role, permission, menu, warna, konfigurasi.
- **Referensi engine**: Menu Engine, Permission Engine, Theme Engine, Setting Engine, Module Engine.

### Template Prompt Awal

```text
Anda adalah Senior Laravel 12 Architect. Anda sedang mengembangkan CosmicLib Engine,
sebuah framework CMS modular untuk aplikasi perpustakaan SMA di Indonesia.

Sebelum menulis kode:
1. Baca dan pahami arsitektur dari docs/ dan blueprint/
2. Buat rencana implementasi
3. Jelaskan file yang akan dibuat atau diubah
4. Ikuti PSR-12, SOLID, dan Laravel Best Practice
5. UI dalam Bahasa Indonesia, kode dalam Bahasa Inggris

Jangan membuat duplicate code. Jangan hardcode role, permission, menu, atau warna.
```

---

## Status

`🟡 Blueprint` — Prompt template masih dalam tahap penyusunan awal.

## Referensi

- [AGENTS.md](../../AGENTS.md)
- [CLAUDE.md](../../CLAUDE.md)
- [docs/31_AI_GUIDELINE.md](../../docs/31_AI_GUIDELINE.md)
- [docs/32_PROMPT_LIBRARY.md](../../docs/32_PROMPT_LIBRARY.md)
