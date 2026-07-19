# 19 — Installer Engine

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

Installer Engine menyediakan **wizard instalasi berbasis web** untuk CosmicLib. Engine ini memandu administrator melalui proses setup awal sistem — dari verifikasi requirement server, konfigurasi database, hingga pembuatan akun administrator pertama — tanpa perlu akses terminal.

**Fungsi utama:**
- Verifikasi PHP extensions dan requirement server
- Konfigurasi koneksi database secara interaktif
- Menjalankan migrasi dan seeder database
- Pembuatan akun administrator pertama
- Konfigurasi environment variables (.env)
- Penguncian installer setelah setup selesai (file lock)
- Panduan langkah demi langkah via web wizard

---

## Arsitektur

```
┌─────────────────────────────────────────────────────────────┐
│                    Installer Engine                         │
│                                                             │
│  ┌──────────────────────────────────────────────────────┐  │
│  │                Installation Wizard                    │  │
│  │                                                       │  │
│  │  Step 1     Step 2     Step 3     Step 4     Step 5  │  │
│  │  Welcome ──▶ Reqmt  ──▶  DB   ──▶  Env   ──▶  Admin │  │
│  │               Check    Config    Config    Account   │  │
│  │                              ──▶  Step 6             │  │
│  │                               Run Migrations         │  │
│  │                              ──▶  Step 7             │  │
│  │                               Finished 🎉            │  │
│  └──────────────────────────────────────────────────────┘  │
│                                                             │
│  ┌─────────────┐    ┌──────────────┐    ┌───────────────┐  │
│  │Requirement  │    │  DB Tester   │    │  Env Writer   │  │
│  │  Checker    │    │  (PDO test)  │    │  (.env file)  │  │
│  └─────────────┘    └──────────────┘    └───────────────┘  │
│                                                             │
│  ┌─────────────┐    ┌──────────────┐    ┌───────────────┐  │
│  │  Migrator   │    │ Admin Creator│    │  Lock File    │  │
│  │  (Artisan)  │    │  (Seeder)    │    │  (installed)  │  │
│  └─────────────┘    └──────────────┘    └───────────────┘  │
└─────────────────────────────────────────────────────────────┘
```

### Route Guard

```
Request to /install/*
    │
    ├── Check: storage/installed exists?
    │       │
    │   [YES]│──▶ Abort 403 (Installer Disabled)
    │       │
    │   [NO] └──▶ Allow installer access

Request to main application
    │
    ├── Check: storage/installed exists?
    │       │
    │   [NO] │──▶ Redirect to /install
    │       │
    │   [YES]└──▶ Allow normal access
```

---

## Komponen

### 1. Controller — `InstallerController`

```
InstallerController
├── welcome(): View         // Step 1: Selamat datang
├── requirements(): View    // Step 2: Cek requirement
├── database(): View        // Step 3: Form koneksi DB
├── storeDatabase(Request): // Step 3: Simpan & test DB
├── environment(): View     // Step 4: Konfigurasi env
├── storeEnvironment(Request):
├── admin(): View           // Step 5: Buat akun admin
├── storeAdmin(Request):
├── migrate(): View         // Step 6: Jalankan migrasi
├── runMigrations(): Response (JSON)
└── finish(): View          // Step 7: Selesai
```

### 2. Service — `InstallerService`

```
InstallerService
├── checkRequirements(): RequirementResult
├── testDatabaseConnection(array $config): bool
├── writeEnvironmentFile(array $data): bool
├── runMigrations(): MigrationResult
├── runSeeders(): bool
├── createAdminUser(array $data): User
├── createLockFile(): bool
├── isInstalled(): bool
└── getCurrentStep(): int
```

### 3. Service — `RequirementChecker`

```
RequirementChecker
├── checkPhpVersion(): CheckResult      // PHP >= 8.2
├── checkExtensions(): array            // pdo, mbstring, openssl, dll
├── checkPermissions(): array           // storage/, bootstrap/cache/
├── checkDiskSpace(): CheckResult       // min 100MB free
├── checkDatabaseSupport(): CheckResult // PDO MySQL/PostgreSQL
└── getFullReport(): RequirementReport
```

### 4. Form Requests

| Request | Validasi |
|---------|----------|
| `DatabaseConfigRequest` | driver, host, port, database, username, (password opsional) |
| `EnvironmentConfigRequest` | app_name, app_url, timezone, locale, mail settings |
| `AdminAccountRequest` | name, email (valid), password (confirmed, min:8) |

### 5. Requirement Matrix

| Requirement | Minimum | Recommended |
|-------------|---------|-------------|
| PHP | 8.2 | 8.3+ |
| MySQL | 8.0 | 8.0+ |
| Memory | 256MB | 512MB+ |
| Disk | 100MB | 1GB+ |
| Extension: pdo_mysql | Required | — |
| Extension: mbstring | Required | — |
| Extension: openssl | Required | — |
| Extension: tokenizer | Required | — |
| Extension: xml | Required | — |
| Extension: ctype | Required | — |
| Extension: json | Required | — |
| Extension: bcmath | Required | — |
| Extension: fileinfo | Required | — |
| Extension: gd / imagick | Required (pilih 1) | — |
| storage/ writable | Required | — |
| bootstrap/cache/ writable | Required | — |

---

## Lifecycle

### Installation Wizard Steps

```
Step 1: Welcome
  └── Tampilkan halaman sambutan, syarat & ketentuan
      └── [Mulai Instalasi] ──▶ Step 2

Step 2: Requirement Check
  └── RequirementChecker::getFullReport()
  └── Tampilkan status setiap requirement
      ├── [GAGAL] Tampilkan panduan perbaikan
      └── [LULUS] ──▶ Step 3

Step 3: Database Configuration
  └── Form: DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASS
  └── [Test Koneksi] ──▶ PDO connect test
      ├── [GAGAL] Tampilkan error message
      └── [SUKSES] Simpan ke session ──▶ Step 4

Step 4: Environment Configuration
  └── Form: APP_NAME, APP_URL, TIMEZONE, LOCALE
  └── InstallerService::writeEnvironmentFile()
      └── [SUKSES] ──▶ Step 5

Step 5: Admin Account
  └── Form: nama, email, password admin pertama
  └── [Lanjut] ──▶ Step 6

Step 6: Run Migration
  └── AJAX call: InstallerService::runMigrations()
  └── Progress bar saat migrasi berjalan
  └── InstallerService::createAdminUser()
  └── InstallerService::runSeeders()
      └── [SUKSES] ──▶ Step 7

Step 7: Finish
  └── InstallerService::createLockFile() → storage/installed
  └── Tampilkan credentials admin
  └── [Login ke Sistem]
```

### Lock File Mechanism

```php
// File: storage/installed
// Isi: timestamp instalasi selesai

// Check di middleware:
if (File::exists(storage_path('installed'))) {
    abort(403, 'Installer sudah dinonaktifkan');
}
```

---

## Konfigurasi

### Installer Routes

```php
// routes/web.php atau routes/install.php
Route::group(['prefix' => 'install', 'middleware' => ['installer.lock']], function () {
    Route::get('/',              [InstallerController::class, 'welcome']);
    Route::get('/requirements',  [InstallerController::class, 'requirements']);
    Route::get('/database',      [InstallerController::class, 'database']);
    Route::post('/database',     [InstallerController::class, 'storeDatabase']);
    Route::get('/environment',   [InstallerController::class, 'environment']);
    Route::post('/environment',  [InstallerController::class, 'storeEnvironment']);
    Route::get('/admin',         [InstallerController::class, 'admin']);
    Route::post('/admin',        [InstallerController::class, 'storeAdmin']);
    Route::get('/migrate',       [InstallerController::class, 'migrate']);
    Route::post('/migrate/run',  [InstallerController::class, 'runMigrations']);
    Route::get('/finish',        [InstallerController::class, 'finish']);
});
```

### Middleware — `InstallerLock`

```php
// Cegah akses installer jika sudah terinstall
class InstallerLock
{
    public function handle(Request $request, Closure $next)
    {
        if (File::exists(storage_path('installed'))) {
            abort(403, 'Aplikasi sudah terinstall.');
        }
        return $next($request);
    }
}
```

### Middleware — `EnsureInstalled`

```php
// Redirect ke installer jika belum terinstall
class EnsureInstalled
{
    public function handle(Request $request, Closure $next)
    {
        if (!File::exists(storage_path('installed')) && 
            !$request->is('install*')) {
            return redirect('/install');
        }
        return $next($request);
    }
}
```

---

## Integrasi

### Dengan Core Engine
- Installer menginisialisasi Core Engine setelah setup selesai
- Menjalankan `php artisan key:generate`
- Menjalankan `php artisan storage:link`

### Dengan Setting Engine
- Seeder default settings dijalankan saat instalasi
- APP_NAME, timezone, locale disimpan ke tabel `settings`

### Dengan User Engine + Auth Engine
- Admin pertama dibuat via `UserService::create()` dengan role `super-admin`
- Password di-hash, email diverifikasi otomatis

### Dengan License Engine
- Setelah instalasi selesai, tampilkan form aktivasi lisensi (opsional)
- `LicenseService::activate()` dipanggil saat step finish

### Dengan Permission Engine
- Default roles dan permissions di-seed saat instalasi
- `RoleSeeder`, `PermissionSeeder` dijalankan otomatis

---

## AI Rules

```yaml
installer_engine_rules:
  - JANGAN izinkan akses installer jika storage/installed sudah ada
  - WAJIB validasi semua requirement sebelum melanjutkan instalasi
  - JANGAN simpan kredensial database dalam session terlalu lama — flush setelah migrasi
  - WAJIB run migrasi dalam try-catch, rollback jika gagal
  - JANGAN expose stack trace ke UI saat error instalasi
  - WAJIB buat lock file segera setelah instalasi selesai
  - JANGAN biarkan wizard bisa diakses ulang setelah instalasi (403)
  - WAJIB hash password admin sebelum disimpan
```

---

## Best Practice

1. **Single-Use** — Installer hanya dapat dijalankan sekali; lock file mencegah pengulangan
2. **Rollback on Failure** — Jika migrasi gagal, jangan buat lock file
3. **Clear Feedback** — Setiap langkah menampilkan status jelas (✅/❌)
4. **AJAX Migration** — Jalankan migrasi via AJAX dengan progress indicator
5. **Secure Storage** — Jangan tampilkan kredensial DB setelah step 3
6. **Validation** — Semua input form divalidasi server-side
7. **Timeout Handling** — Migrasi besar memerlukan timeout yang cukup (`max_execution_time`)
8. **Error Logging** — Log semua error instalasi ke `storage/logs/installer.log`

---

## Checklist

### Implementasi
- [ ] `InstallerController` dengan semua steps
- [ ] `InstallerService` dengan semua method
- [ ] `RequirementChecker` dengan semua pengecekan
- [ ] Lock file mechanism
- [ ] Middleware `InstallerLock` dan `EnsureInstalled`
- [ ] Semua Form Request dengan validasi
- [ ] AJAX migration runner dengan progress bar
- [ ] Error handling dan rollback

### UI Wizard
- [ ] Halaman Welcome dengan logo dan intro
- [ ] Checklist requirement dengan status visual
- [ ] Form database dengan tombol Test Koneksi
- [ ] Form environment configuration
- [ ] Form admin account
- [ ] Progress bar migrasi (AJAX)
- [ ] Halaman finish dengan summary dan link login

### Testing
- [ ] Test requirement checker
- [ ] Test database connection tester
- [ ] Test environment file writer
- [ ] Test migration runner
- [ ] Test lock file creation
- [ ] Test middleware InstallerLock
- [ ] Test middleware EnsureInstalled

---

## Roadmap

| Versi | Fitur | Status |
|-------|-------|--------|
| v1.0 | Web wizard 7 langkah | Planned |
| v1.1 | CLI installer (`php artisan install`) | Planned |
| v1.2 | Demo mode installer (tanpa DB nyata) | Planned |
| v2.0 | Multi-language installer | Future |
| v2.1 | Docker-compose generator | Future |
| v2.2 | Cloud installer (AWS/GCP config) | Future |

---

## Referensi

- [16_SYSTEM_SETTING_ENGINE.md](16_SYSTEM_SETTING_ENGINE.md) — Setting Engine
- [17_AUTH_ENGINE.md](17_AUTH_ENGINE.md) — Auth Engine
- [18_USER_ENGINE.md](18_USER_ENGINE.md) — User Engine
- [10_PERMISSION_ENGINE.md](10_PERMISSION_ENGINE.md) — Permission Engine
- [22_LICENSE_ENGINE.md](22_LICENSE_ENGINE.md) — License Engine
- [38_DEPLOYMENT.md](38_DEPLOYMENT.md) — Deployment Guide

---

*Dokumen ini adalah bagian dari CosmicLib Engine Documentation Suite.*
*Dibuat: 2026 | Terakhir diperbarui: 2026-07-19*