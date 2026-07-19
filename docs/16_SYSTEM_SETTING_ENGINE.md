# 16 — System Setting Engine

> **CosmicLib Engine Documentation**
> Versi: 1.0.0 | Status: Blueprint | Bahasa: ID (UI) / EN (Code)

---

## Daftar Isi

1. [Tujuan](#tujuan)
2. [Arsitektur](#arsitektur)
3. [Komponen](#komponen)
4. [Lifecycle](#lifecycle)
5. [Konfigurasi](#konfigurasi)
6. [Integrasi](#integrasi)
7. [AI Rules](#ai-rules)
8. [Best Practice](#best-practice)
9. [Checklist](#checklist)
10. [Roadmap](#roadmap)

---

## Tujuan

System Setting Engine adalah komponen inti yang menyediakan **pusat konfigurasi global** untuk seluruh sistem CosmicLib. Engine ini mengelola semua pengaturan aplikasi yang bersifat dinamis — tanpa memerlukan perubahan kode atau restart server.

**Fungsi utama:**
- Menyimpan dan mengambil konfigurasi sistem secara terpusat
- Mendukung pengelompokan setting berdasarkan grup/kategori
- Menyediakan cache layer untuk performa optimal
- Mendukung enkripsi untuk setting sensitif (API key, password)
- Memungkinkan override setting per-tenant (multi-library)

---

## Arsitektur

```
┌─────────────────────────────────────────────────────────────┐
│                     Setting Engine                          │
│                                                             │
│  ┌─────────────┐    ┌──────────────┐    ┌───────────────┐  │
│  │  Setting UI │───▶│ SettingForm  │───▶│SettingService │  │
│  │  (Admin)    │    │  (Request)   │    │               │  │
│  └─────────────┘    └──────────────┘    └───────┬───────┘  │
│                                                 │          │
│  ┌─────────────┐    ┌──────────────┐    ┌───────▼───────┐  │
│  │ Config Cache│◀───│  Repository  │◀───│  Setting DB   │  │
│  │ (Redis/File)│    │  Layer       │    │  (settings    │  │
│  └─────────────┘    └──────────────┘    │   table)      │  │
│                                         └───────────────┘  │
│                                                             │
│  ┌─────────────────────────────────────────────────────┐   │
│  │              Setting Groups                          │   │
│  │  general | library | mail | storage | security      │   │
│  │  theme   | module  | api  | backup  | notification  │   │
│  └─────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────┘
```

### Pola Akses Setting

```
Application Boot
      │
      ▼
Setting::all() ──▶ Cache Check ──▶ [HIT] Return from Cache
                        │
                    [MISS]
                        │
                        ▼
                   DB Query ──▶ settings table ──▶ Store in Cache ──▶ Return
```

---

## Komponen

### 1. Model — `Setting`

```php
// app/Models/Setting.php
namespace App\Models;

class Setting extends Model
{
    protected $fillable = [
        'group',        // Kategori setting (general, library, mail, dll)
        'key',          // Kunci unik dalam grup
        'value',        // Nilai setting (encrypted jika is_encrypted=true)
        'type',         // string | boolean | integer | json | file
        'label',        // Label tampilan UI (Bahasa Indonesia)
        'description',  // Deskripsi / help text
        'is_encrypted', // Enkripsi nilai sensitif
        'is_public',    // Apakah bisa diakses tanpa autentikasi
        'is_editable',  // Apakah bisa diedit via UI
        'sort_order',   // Urutan tampilan
    ];

    protected $casts = [
        'is_encrypted' => 'boolean',
        'is_public'    => 'boolean',
        'is_editable'  => 'boolean',
        'sort_order'   => 'integer',
    ];
}
```

### 2. Service — `SettingService`

```
SettingService
├── get(string $key, mixed $default = null): mixed
├── set(string $key, mixed $value): bool
├── getGroup(string $group): Collection
├── updateGroup(string $group, array $data): bool
├── forget(string $key): bool
├── flushCache(): void
├── encrypt(string $value): string
└── decrypt(string $value): string
```

### 3. Repository — `SettingRepository`

```
SettingRepository
├── findByKey(string $key): ?Setting
├── findByGroup(string $group): Collection
├── upsert(string $key, mixed $value): Setting
├── deleteByKey(string $key): bool
└── getAllPublic(): Collection
```

### 4. Helper — `setting()`

```php
// Global helper function
function setting(string $key, mixed $default = null): mixed
{
    return app(SettingService::class)->get($key, $default);
}
```

### 5. Facade — `Setting`

```php
// Penggunaan via Facade
Setting::get('general.site_name');
Setting::set('library.max_loan_days', 14);
Setting::getGroup('mail');
```

### 6. Controller — `SettingController`

```
SettingController
├── index(): View       // Halaman daftar setting
├── show(group): View   // Setting per grup
├── update(Request): Redirect  // Simpan perubahan
└── reset(group): Redirect     // Reset ke default
```

### 7. Form Request — `UpdateSettingRequest`

Validasi input sebelum disimpan ke database.

### 8. Setting Groups

| Grup | Kunci Utama | Deskripsi |
|------|-------------|-----------|
| `general` | site_name, site_description, timezone, locale | Pengaturan umum |
| `library` | max_loan_days, max_books_per_member, fine_per_day | Aturan perpustakaan |
| `mail` | driver, host, port, username, from_address | Konfigurasi email |
| `storage` | driver, path, max_file_size, allowed_types | Manajemen file |
| `security` | session_lifetime, password_min_length, 2fa_enabled | Keamanan |
| `theme` | active_theme, color_primary, dark_mode | Tampilan |
| `notification` | email_enabled, sms_enabled, push_enabled | Notifikasi |
| `api` | rate_limit, token_expiry, public_key | Pengaturan API |
| `backup` | auto_backup, backup_time, retention_days | Cadangan data |
| `module` | enabled_modules | Modul aktif |

---

## Lifecycle

### Boot Sequence

```
1. AppServiceProvider::boot()
   └── SettingService::loadFromDatabase()
       └── Cache::remember('settings', ...)
           └── Setting::all()

2. Runtime Request
   └── setting('key') → SettingService::get()
       └── Cache check → DB fallback

3. Setting Update
   └── SettingController::update()
       └── UpdateSettingRequest (validasi)
           └── SettingService::updateGroup()
               └── DB::transaction()
                   ├── Setting::upsert()
                   └── Cache::flush('settings')

4. Cache Invalidation
   └── Triggered on: set(), updateGroup(), reset()
       └── Cache::tags(['settings'])->flush()
```

### Setting Value Types

| Type | Penyimpanan | Cast Output |
|------|-------------|-------------|
| `string` | VARCHAR | string |
| `boolean` | TINYINT | bool |
| `integer` | TEXT (cast) | int |
| `json` | TEXT (JSON) | array |
| `file` | VARCHAR (path) | string |
| `encrypted` | TEXT (encrypted) | string (decrypted) |

---

## Konfigurasi

### Database Schema — Tabel `settings`

```sql
CREATE TABLE settings (
    id            BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    group         VARCHAR(50)  NOT NULL DEFAULT 'general',
    key           VARCHAR(100) NOT NULL,
    value         TEXT         NULL,
    type          VARCHAR(20)  NOT NULL DEFAULT 'string',
    label         VARCHAR(150) NOT NULL,
    description   TEXT         NULL,
    is_encrypted  TINYINT(1)   NOT NULL DEFAULT 0,
    is_public     TINYINT(1)   NOT NULL DEFAULT 0,
    is_editable   TINYINT(1)   NOT NULL DEFAULT 1,
    sort_order    INT          NOT NULL DEFAULT 0,
    created_at    TIMESTAMP    NULL,
    updated_at    TIMESTAMP    NULL,

    UNIQUE KEY uq_settings_key (group, key),
    INDEX idx_settings_group (group),
    INDEX idx_settings_public (is_public)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### Default Settings (Seeder)

```
general.site_name         = "CosmicLib"
general.site_description  = "Sistem Manajemen Perpustakaan Digital"
general.timezone          = "Asia/Jakarta"
general.locale            = "id"
general.date_format       = "d/m/Y"
general.currency          = "IDR"
library.max_loan_days     = 14
library.max_books_member  = 3
library.fine_per_day      = 500
library.grace_period_days = 1
mail.driver               = "smtp"
storage.max_file_size     = 10240  (KB)
security.session_lifetime = 120    (menit)
```

### File Konfigurasi Laravel

```php
// config/setting.php
return [
    'cache_key'     => 'cosmiclib_settings',
    'cache_ttl'     => 3600,          // detik
    'cache_driver'  => env('CACHE_DRIVER', 'file'),
    'encrypt_key'   => env('SETTING_ENCRYPT_KEY'),
    'default_group' => 'general',
];
```

---

## Integrasi

### Dengan Core Engine
- Setting Engine diinisialisasi saat boot oleh `CoreServiceProvider`
- Semua engine lain mengonsumsi setting via `setting()` helper

### Dengan Theme Engine
```php
// Theme Engine membaca setting warna
$primaryColor = setting('theme.color_primary', '#6C63FF');
$darkMode     = setting('theme.dark_mode', false);
```

### Dengan Module Engine
```php
// Module Engine membaca daftar modul aktif
$enabledModules = setting('module.enabled_modules', []);
```

### Dengan Mail/Notification Engine
```php
// Notification Engine membaca konfigurasi email
config(['mail.default' => setting('mail.driver')]);
config(['mail.mailers.smtp.host' => setting('mail.host')]);
```

### Dengan Permission Engine
- Setting admin hanya dapat diubah oleh role dengan permission `settings.edit`
- Setting publik dapat dibaca oleh semua user

### Dengan API Engine
- Endpoint `GET /api/v1/settings/public` mengembalikan setting `is_public = true`
- Endpoint `PUT /api/v1/settings/{group}` memerlukan token admin

---

## AI Rules

```yaml
setting_engine_rules:
  - JANGAN pernah hardcode nilai konfigurasi — selalu baca dari Setting Engine
  - JANGAN gunakan env() di luar file config/ — gunakan setting() helper
  - JANGAN simpan API key atau password tanpa enkripsi (is_encrypted = true)
  - WAJIB invalidate cache setiap kali setting diubah
  - WAJIB validasi tipe data sebelum menyimpan (UpdateSettingRequest)
  - JANGAN expose setting is_public=false ke endpoint publik
  - SELALU gunakan grup yang tepat untuk mengorganisir setting
  - WAJIB bungkus operasi DB dalam try-catch dan log error
```

---

## Best Practice

1. **Cache First** — Selalu baca dari cache, bukan langsung dari DB
2. **Grup yang Jelas** — Setiap setting memiliki grup yang tepat dan konsisten
3. **Enkripsi Wajib** — Semua nilai sensitif (API key, password) harus dienkripsi
4. **Default Value** — Selalu sediakan default value di helper `setting('key', $default)`
5. **Validasi Ketat** — Gunakan Form Request untuk validasi tipe dan constraint
6. **Audit Log** — Catat setiap perubahan setting (siapa, kapan, nilai lama vs baru)
7. **Setting Publik** — Batasi setting `is_public=true` hanya untuk konfigurasi non-sensitif
8. **Reset Capability** — Sediakan fitur reset ke nilai default per grup

---

## Checklist

### Implementasi

- [ ] Tabel `settings` dibuat dengan schema yang benar
- [ ] Model `Setting` dengan casts dan enkripsi
- [ ] `SettingService` dengan cache layer
- [ ] `SettingRepository` dengan operasi CRUD
- [ ] Global helper function `setting()`
- [ ] Facade `Setting` terdaftar di `config/app.php`
- [ ] `SettingController` dengan semua method
- [ ] `UpdateSettingRequest` dengan validasi lengkap
- [ ] Seeder untuk default settings
- [ ] Cache invalidation saat update
- [ ] Enkripsi/dekripsi untuk setting sensitif

### UI Admin

- [ ] Halaman daftar setting per grup
- [ ] Form edit setting dinamis berdasarkan type
- [ ] Toggle switch untuk boolean
- [ ] File upload untuk type `file`
- [ ] Preview perubahan sebelum simpan
- [ ] Tombol reset ke default
- [ ] Notifikasi sukses/gagal

### Testing

- [ ] Unit test SettingService
- [ ] Unit test enkripsi/dekripsi
- [ ] Feature test update setting via HTTP
- [ ] Test cache invalidation
- [ ] Test permission (admin only untuk edit)

---

## Roadmap

| Versi | Fitur | Status |
|-------|-------|--------|
| v1.0 | Setting CRUD + Cache + Enkripsi | Planned |
| v1.1 | Import/Export setting (JSON) | Planned |
| v1.2 | Multi-tenant setting override | Planned |
| v1.3 | Setting versioning (history) | Planned |
| v2.0 | Real-time setting sync via WebSocket | Future |
| v2.1 | Setting templates per library type | Future |

---

## Referensi

- [07_CORE_ENGINE.md](07_CORE_ENGINE.md) — Core Engine Architecture
- [10_PERMISSION_ENGINE.md](10_PERMISSION_ENGINE.md) — Permission Engine
- [09_THEME_ENGINE.md](09_THEME_ENGINE.md) — Theme Engine Integration
- [17_AUTH_ENGINE.md](17_AUTH_ENGINE.md) — Auth Engine
- [23_API_ENGINE.md](23_API_ENGINE.md) — API Engine

---

*Dokumen ini adalah bagian dari CosmicLib Engine Documentation Suite.*
*Dibuat: 2026 | Terakhir diperbarui: 2026-07-19*