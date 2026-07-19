# ðŸŒŒ 38 — Prompt Library

## Deskripsi

Dokumen ini mendokumentasikan koleksi **Prompt Library** — kumpulan template prompt yang telah diuji dan dioptimalkan untuk berbagai tugas pengembangan CosmicLib Engine menggunakan AI coding assistant.

## Tujuan

Menyediakan referensi prompt siap pakai yang menghasilkan output berkualitas tinggi dan konsisten dari berbagai AI assistant, sehingga mempercepat proses pengembangan tanpa mengorbankan standar kualitas.

## Ruang Lingkup

- Prompt untuk pembuatan modul baru
- Prompt untuk pembuatan controller dan service
- Prompt untuk pembuatan migration dan seeder
- Prompt untuk pembuatan test
- Prompt untuk debugging dan troubleshooting
- Prompt untuk dokumentasi
- Prompt untuk code review

---

## 🗂️ Table of Contents

1. [Cara Menggunakan Prompt Library](#cara-menggunakan-prompt-library)
2. [Prompt: Pembuatan Modul Baru](#prompt-pembuatan-modul-baru)
3. [Prompt: Controller & Service Layer](#prompt-controller--service-layer)
4. [Prompt: Database Migration](#prompt-database-migration)
5. [Prompt: Unit & Feature Test](#prompt-unit--feature-test)
6. [Prompt: Debugging](#prompt-debugging)
7. [Prompt: Dokumentasi](#prompt-dokumentasi)
8. [Prompt: Code Review](#prompt-code-review)

---

## Status

`🟡 Blueprint` — Prompt template dalam tahap penyusunan awal.

---

## ⚙️ Kerangka Sistem

### Cara Menggunakan Prompt Library

*Placeholder: Setiap prompt template memiliki variabel yang ditandai `{VARIABLE_NAME}`. Ganti variabel tersebut dengan nilai yang sesuai sebelum mengirimkan ke AI assistant. Setiap prompt sudah menyertakan konteks CosmicLib Engine.*

### Prompt: Pembuatan Modul Baru

*Placeholder:*
```text
Buatlah modul baru bernama {MODULE_NAME} untuk CosmicLib Engine.
Modul ini bertanggung jawab untuk {MODULE_DESCRIPTION}.

Ikuti arsitektur modular CosmicLib:
- Service Provider: {Module}ServiceProvider
- Controller: {Module}Controller di namespace Modules\{Module}
- Service: {Module}Service di namespace Modules\{Module}\Services
- Model: sesuai kebutuhan di namespace Modules\{Module}\Models
- Migration: sesuai skema di docs/24_DATABASE_SCHEMA.md
- Route: prefix {module-slug}, middleware auth
- View: Blade template dengan layout dari Theme Engine

Patuhi PSR-12, SOLID, dan coding standard CosmicLib.
UI dalam Bahasa Indonesia, kode dalam Bahasa Inggris.
```

### Prompt: Controller & Service Layer

*Placeholder:*
```text
Buatlah controller dan service layer untuk fitur {FEATURE_NAME}.
Controller harus thin — semua logika bisnis di service layer.
Gunakan Form Request untuk validasi.
Gunakan dependency injection untuk service resolution.
Patuhi standar di docs/23_CODING_STANDARD.md.
```

### Prompt: Database Migration

*Placeholder:*
```text
Buatlah migration untuk tabel {TABLE_NAME} sesuai skema di docs/24_DATABASE_SCHEMA.md.
Primary key: bigint unsigned AUTO_INCREMENT.
Sertakan timestamps, soft deletes jika diperlukan.
Definisikan foreign key secara eksplisit.
Tambahkan indeks untuk kolom yang sering diquery.
```

### Prompt: Unit & Feature Test

*Placeholder:*
```text
Buatlah test untuk {FEATURE_NAME}:
- Unit test untuk {Service}Service
- Feature test untuk endpoint {ENDPOINT}
- Test case: happy path, validation error, authorization error
Gunakan factory dan faker untuk test data.
```

### Prompt: Debugging

*Placeholder:*
```text
Saya mengalami error berikut di CosmicLib Engine:
{ERROR_MESSAGE}

File terkait: {FILE_PATH}
Konteks: {CONTEXT}

Analisis penyebab error dan berikan solusi yang sesuai dengan arsitektur CosmicLib.
Jangan mengubah arsitektur — perbaiki sesuai pola yang sudah ada.
```

### Prompt: Dokumentasi

*Placeholder:*
```text
Buatlah dokumentasi untuk {COMPONENT_NAME} sesuai template standar CosmicLib:
- Deskripsi
- Tujuan
- Ruang Lingkup
- Table of Contents
- Status
- Kerangka Sistem
- Referensi
- Catatan
```

### Prompt: Code Review

*Placeholder:*
```text
Review kode berikut terhadap standar CosmicLib Engine:
{CODE_BLOCK}

Periksa:
- Kepatuhan PSR-12 dan SOLID
- Tidak ada duplicate code
- Tidak ada hardcoded values
- Bahasa UI (Indonesia) vs kode (Inggris)
- Error handling yang memadai
- Potensi masalah keamanan
```

---

## Referensi

- [31_AI_GUIDELINE.md](31_AI_GUIDELINE.md)
- [23_CODING_STANDARD.md](23_CODING_STANDARD.md)
- [prompts/claude/README.md](../prompts/claude/README.md)
- [prompts/chatgpt/README.md](../prompts/chatgpt/README.md)

## Catatan

- Prompt library ini terus diperbarui seiring perkembangan proyek.
- Setiap prompt sudah dioptimalkan untuk CosmicLib Engine — jangan gunakan prompt generik.
- Hasil dari AI tetap harus di-review oleh manusia sebelum di-merge.
