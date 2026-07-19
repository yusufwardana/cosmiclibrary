# 🚀 CosmicLib Engine

Sistem Informasi Perpustakaan SMA berbasis Laravel 13.x.

> **Status**: Phase 2 — Implementation & Scaffold  
> **Version**: 1.0.0-alpha

---

## 📋 Prasyarat

- PHP >= 8.3
- MySQL >= 8.0 / MariaDB >= 10.4
- Node.js >= 18.x (untuk Vite)
- Composer >= 2.x

## 🛠️ Instalasi

```bash
composer install
cp .env.example .env
php artisan key:generate
npm install && npm run build
php artisan migrate --seed
php artisan serve
```

## 🏗️ Arsitektur

| Engine | Fungsi |
|:---|---:|
| Core Engine | Lifecycle, DI, service providers |
| Module Engine | Loading & manajemen modul |
| Theme Engine | Manajemen tema visual |
| Permission Engine | ACL berbasis role |
| Menu Engine | Navigasi dinamis |
| Setting Engine | Konfigurasi key-value |
| Installer Engine | Web installer wizard |

## 📄 Lisensi

MIT