# 🌌 04 — Tech Stack

## Deskripsi

Dokumen ini mendokumentasikan seluruh **teknologi, framework, dan pustaka** yang digunakan dalam pengembangan CosmicLib Engine.

## Tujuan

Menyediakan referensi teknis tunggal tentang stack teknologi proyek, sehingga setiap pengembang mengetahui versi dan alasan pemilihan setiap komponen teknologi.

## Ruang Lingkup

- Backend framework dan bahasa pemrograman
- Frontend framework dan asset bundling
- Database engine
- Development tools dan linters
- Target hosting environment

---

## 🗂️ Table of Contents

1. [Backend Stack](#backend-stack)
2. [Frontend Stack](#frontend-stack)
3. [Database](#database)
4. [Development Tools](#development-tools)
5. [Target Environment](#target-environment)

---

## Status

`🟡 Blueprint` — Dokumen dalam tahap perancangan.

---

## ⚙️ Kerangka Sistem

### Backend Stack

| Teknologi | Versi | Fungsi |
|:---|:---|:---|
| PHP | ≥ 8.2 (rekomendasi 8.3) | Bahasa pemrograman utama |
| Laravel | 12.x | Framework backend utama |
| Composer | 2.x | PHP dependency manager |

### Frontend Stack

| Teknologi | Versi | Fungsi |
|:---|:---|:---|
| Blade | (Laravel built-in) | Template engine |
| Bootstrap | 5.x | CSS framework |
| Vite | 6.x | Asset bundling & HMR |
| Node.js | ≥ 18.x | JavaScript runtime untuk build tools |
| NPM | ≥ 9.x | JavaScript package manager |

### Database

| Teknologi | Versi | Fungsi |
|:---|:---|:---|
| MySQL | ≥ 8.0 | Database utama |
| MariaDB | ≥ 10.4 | Alternatif (kompatibel) |

### Development Tools

*Placeholder:*
- **Laravel Pint**: PHP code formatter (PSR-12)
- **PHPUnit**: Unit & feature testing
- **Laravel Debugbar**: Development debugging (non-production)
- **EditorConfig**: Cross-IDE formatting consistency

### Target Environment

*Placeholder:*
- **Produksi**: Shared Hosting cPanel dengan PHP 8.2+, MySQL 8.0+, terminal & Node.js
- **Development**: Local development menggunakan `php artisan serve` + Vite dev server
- **Testing**: PHPUnit dengan SQLite in-memory database

---

## Referensi

- [PROJECT_MANIFEST.md](../PROJECT_MANIFEST.md)
- [03_ARCHITECTURE.md](03_ARCHITECTURE.md)
- [27_DEPLOYMENT.md](27_DEPLOYMENT.md)
- [28_CPANEL_DEPLOYMENT.md](28_CPANEL_DEPLOYMENT.md)

## Catatan

- Pemilihan teknologi mengutamakan kompatibilitas dengan shared hosting cPanel.
- Hindari teknologi yang memerlukan akses root (Redis, Supervisor, WebSocket server).
- Semua versi minimum harus divalidasi saat instalasi oleh Installer Engine.
