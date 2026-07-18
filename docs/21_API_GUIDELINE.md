# 🌌 21 — API Guideline

## Deskripsi

Dokumen ini menyusun panduan perancangan dan implementasi **API RESTful** untuk CosmicLib Engine, mencakup konvensi penamaan, struktur response, autentikasi, versioning, dan error handling.

## Tujuan

Menetapkan standar yang konsisten untuk seluruh API endpoint yang akan dibangun dalam CosmicLib Engine, sehingga semua modul dan plugin mengikuti pola yang sama dan mudah diintegrasikan oleh pihak ketiga.

## Ruang Lingkup

- Konvensi penamaan URL dan resource
- Struktur JSON response standar
- Autentikasi API (Sanctum/token-based)
- API versioning strategy
- Rate limiting dan throttling
- Dokumentasi API otomatis
- Error response standardization

---

## 🗂️ Table of Contents

1. [Konvensi Penamaan Endpoint](#konvensi-penamaan-endpoint)
2. [Struktur Response JSON](#struktur-response-json)
3. [Autentikasi & Otorisasi API](#autentikasi--otorisasi-api)
4. [Versioning Strategy](#versioning-strategy)
5. [Rate Limiting](#rate-limiting)
6. [Error Handling](#error-handling)
7. [Dokumentasi API](#dokumentasi-api)

---

## Status

`🟡 Blueprint` — Dokumen dalam tahap perancangan.

---

## ⚙️ Kerangka Sistem

### Konvensi Penamaan Endpoint

*Placeholder: Gunakan format RESTful standar:*
```
GET    /api/v1/books              → Daftar buku
GET    /api/v1/books/{id}         → Detail buku
POST   /api/v1/books              → Tambah buku
PUT    /api/v1/books/{id}         → Update buku
DELETE /api/v1/books/{id}         → Hapus buku
```

### Struktur Response JSON

*Placeholder: Format response standar:*
```json
{
  "success": true,
  "message": "Data berhasil diambil",
  "data": {},
  "meta": {
    "current_page": 1,
    "total": 100,
    "per_page": 15
  }
}
```

### Autentikasi & Otorisasi API

*Placeholder: Menggunakan Laravel Sanctum untuk autentikasi token-based. Setiap request API harus menyertakan header `Authorization: Bearer {token}`. Otorisasi mengikuti Permission Engine.*

### Versioning Strategy

*Placeholder: API versioning menggunakan URL prefix (`/api/v1/`, `/api/v2/`). Versi lama tetap didukung selama minimal 6 bulan setelah versi baru dirilis.*

### Rate Limiting

*Placeholder: Pembatasan request per menit menggunakan Laravel Rate Limiter. Default: 60 request/menit untuk user terautentikasi, 30 request/menit untuk guest.*

### Error Handling

*Placeholder: Format error response standar dengan HTTP status code yang sesuai (400, 401, 403, 404, 422, 500).*

### Dokumentasi API

*Placeholder: Dokumentasi otomatis menggunakan tool seperti Scribe atau L5-Swagger. Setiap endpoint harus memiliki deskripsi, parameter, dan contoh response.*

---

## Referensi

- [07_CORE_ENGINE.md](07_CORE_ENGINE.md)
- [10_PERMISSION_ENGINE.md](10_PERMISSION_ENGINE.md)
- [22_SECURITY_GUIDELINE.md](22_SECURITY_GUIDELINE.md)
- [23_CODING_STANDARD.md](23_CODING_STANDARD.md)

## Catatan

- Semua API harus menggunakan JSON sebagai format data utama.
- Pesan error yang ditampilkan ke pengguna harus dalam Bahasa Indonesia.
- API internal (antar-modul) dan API publik (pihak ketiga) harus dipisahkan secara jelas.
