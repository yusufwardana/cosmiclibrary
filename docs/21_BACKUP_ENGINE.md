# 21 — Backup Engine

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

Backup Engine menyediakan sistem pencadangan data otomatis dan manual untuk CosmicLib. Engine ini memastikan data perpustakaan selalu terlindungi — termasuk database, file media, dan konfigurasi sistem — dengan dukungan penjadwalan, enkripsi backup, dan berbagai target penyimpanan.

**Fungsi utama:**
- Backup database (full dump, incremental)
- Backup file sistem (media, uploads, konfigurasi)
- Penjadwalan backup otomatis via Scheduler
- Enkripsi file backup
- Upload ke remote storage (S3, Google Drive, SFTP)
- Restore dari file backup
- Manajemen retensi backup (hapus backup lama otomatis)
- Notifikasi status backup ke admin

---

## Arsitektur

```
┌─────────────────────────────────────────────────────────────┐
│                     Backup Engine                           │
│                                                             │
│  ┌──────────────┐    ┌─────────────────────────────────┐   │
│  │  Scheduler   │───▶│         BackupService           │   │
│  │  (Artisan)   │    │                                 │   │
│  └──────────────┘    │  ┌───────────┐ ┌─────────────┐ │   │
│                       │  │  DB Dump  │ │  File Zip   │ │   │
│  ┌──────────────┐    │  │ (mysqldump)│ │  (storage/) │ │   │
│  │  Admin UI   │───▶│  └─────┬─────┘ └──────┬──────┘ │   │
│  │  (Manual)   │    │        └──────┬─────────┘       │   │
│  └──────────────┘    │              ▼                  │   │
│                       │      ┌──────────────┐          │   │
│                       │      │  Encryptor   │          │   │
│                       │      │  (AES-256)   │          │   │
│                       │      └──────┬───────┘          │   │
│                       └─────────────│────────────────--┘   │
│                                     │                      │
│         ┌───────────────────────────┼──────────────────┐   │
│         ▼                           ▼                   ▼   │
│  ┌─────────────┐    ┌───────────────────┐    ┌───────────┐ │
│  │ Local Disk  │    │   S3 / R2 Cloud   │    │  SFTP     │ │
│  │ (storage/)  │    │   (Remote)        │    │  Server   │ │
│  └─────────────┘    └───────────────────┘    └───────────┘ │
└─────────────────────────────────────────────────────────────┘
```

---

## Komponen

### 1. Controller — `BackupController`

```
BackupController
├── index(): View              // Dashboard backup
├── create(): JsonResponse     // Buat backup manual
├── download(id): Response     // Download file backup
├── restore(Request): JsonResponse  // Restore dari backup
├── delete(id): JsonResponse   // Hapus backup
└── schedule(): View           // Manajemen jadwal backup
```

### 2. Service — `BackupService`

```
BackupService
├── create(BackupOptions $options): BackupResult
├── createDatabase(): string      // Return path file SQL
├── createFiles(): string         // Return path zip
├── createFull(): string          // Database + Files
├── encrypt(string $path): string
├── upload(string $path, string $disk): bool
├── restore(string $backupFile): RestoreResult
├── restoreDatabase(string $sqlFile): bool
├── restoreFiles(string $zipFile): bool
├── delete(int $backupId): bool
├── getHistory(): Collection
├── enforceRetention(): int       // Hapus backup lama, return count
└── getStats(): BackupStats
```

### 3. Command — `BackupCommand`

```php
// php artisan backup:run
// php artisan backup:clean
// php artisan backup:restore {file}
// php artisan backup:list
```

### 4. Database Table — `backups`

```sql
CREATE TABLE backups (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(255) NOT NULL,
    type            ENUM('database','files','full') NOT NULL,
    disk            VARCHAR(50) NOT NULL DEFAULT 'local',
    path            VARCHAR(500) NOT NULL,
    size_bytes      BIGINT UNSIGNED NULL,
    is_encrypted    TINYINT(1) DEFAULT 0,
    status          ENUM('pending','running','completed','failed') DEFAULT 'pending',
    started_at      TIMESTAMP NULL,
    completed_at    TIMESTAMP NULL,
    checksum        VARCHAR(64) NULL,  -- SHA256
    notes           TEXT NULL,
    error_message   TEXT NULL,
    created_by      BIGINT UNSIGNED NULL,
    created_at      TIMESTAMP NULL,
    updated_at      TIMESTAMP NULL,

    INDEX idx_backups_status (status),
    INDEX idx_backups_type (type),
    INDEX idx_backups_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 5. Backup Options

```php
class BackupOptions
{
    public string $type = 'full';         // database | files | full
    public bool   $encrypt = true;
    public string $disk = 'local';        // local | s3 | sftp
    public string $name = '';             // auto-generate jika kosong
    public array  $excludeTables = [];    // Tabel yang dikecualikan dari dump
    public array  $excludePaths = [];     // Path yang dikecualikan dari zip
}
```

---

## Lifecycle

### Backup Creation Lifecycle

```
1. Trigger (Manual dari UI atau Scheduler)
2. BackupService::create(options)
3. Catat di backups table (status = 'running')
4. Berdasarkan type:
   ├── 'database': mysqldump → compressed .sql.gz
   ├── 'files':    zip storage/ → compressed .zip
   └── 'full':     mysqldump + zip → archive .tar.gz
5. Encrypt jika is_encrypted = true (AES-256)
6. Upload ke disk target (local/S3/SFTP)
7. Verifikasi checksum
8. Update backups table (status = 'completed', size, checksum)
9. Jalankan retention policy (hapus backup > retention_days)
10. Notifikasi admin via email/dashboard
```

### Restore Lifecycle

```
1. Admin pilih backup dari daftar
2. Konfirmasi restore (warning data akan tertimpa)
3. BackupService::restore(path)
4. Aktifkan maintenance mode
5. Decrypt jika perlu
6. Verifikasi checksum
7. Restore database (DROP + reimport SQL)
8. Restore files (extract zip ke storage/)
9. php artisan config:cache
10. php artisan migrate (jika ada migrasi pending)
11. Nonaktifkan maintenance mode
12. Log hasil restore
```

### Retention Policy

```
- Backup harian: simpan 7 hari terakhir
- Backup mingguan: simpan 4 minggu terakhir
- Backup bulanan: simpan 12 bulan terakhir
- Backup manual: simpan selamanya (kecuali dihapus manual)
```

---

## Konfigurasi

### Schedule (`app/Console/Kernel.php`)

```php
$schedule->command('backup:run --type=database')->daily()->at('02:00');
$schedule->command('backup:run --type=full')->weekly()->sundays()->at('01:00');
$schedule->command('backup:clean')->daily()->at('03:00');
```

### Environment Variables

```env
BACKUP_DISK=local               # local | s3 | sftp
BACKUP_ENCRYPT=true
BACKUP_ENCRYPT_KEY=             # 32-char random key
BACKUP_RETENTION_DAILY=7
BACKUP_RETENTION_WEEKLY=4
BACKUP_RETENTION_MONTHLY=12
BACKUP_NOTIFY_EMAIL=admin@example.com

# S3 Config (jika disk = s3)
AWS_BUCKET=cosmiclib-backups
AWS_DEFAULT_REGION=ap-southeast-1

# SFTP Config (jika disk = sftp)
SFTP_BACKUP_HOST=
SFTP_BACKUP_PORT=22
SFTP_BACKUP_USER=
SFTP_BACKUP_KEY_PATH=
```

### Config File

```php
// config/backup.php
return [
    'disks'      => ['local', 's3'],
    'encrypt'    => env('BACKUP_ENCRYPT', true),
    'retention'  => [
        'daily'   => env('BACKUP_RETENTION_DAILY', 7),
        'weekly'  => env('BACKUP_RETENTION_WEEKLY', 4),
        'monthly' => env('BACKUP_RETENTION_MONTHLY', 12),
    ],
    'exclude_tables' => ['sessions', 'personal_access_tokens', 'cache'],
    'exclude_paths'  => ['storage/framework', 'storage/logs'],
    'temp_path'      => storage_path('backup_temp'),
];
```

---

## Integrasi

### Dengan Update Engine
- Backup Engine dipanggil otomatis sebelum update dijalankan

### Dengan Queue Engine
- Proses backup berat dijalankan via Queue (background job)
- `BackupJob` dispatched ke queue `backups`

### Dengan Notification Engine
- Notifikasi sukses/gagal backup ke admin
- Alert jika disk backup hampir penuh (> 90%)

### Dengan Setting Engine
```php
$retentionDays = setting('backup.retention_days', 7);
$autoBackup    = setting('backup.auto_backup', true);
$backupTime    = setting('backup.backup_time', '02:00');
```

### Dengan Log Engine
- Semua event backup dicatat di activity log
- Error detail disimpan di `backups.error_message`

---

## AI Rules

```yaml
backup_engine_rules:
  - WAJIB encrypt semua backup sebelum upload ke remote storage
  - JANGAN simpan encryption key di dalam backup itu sendiri
  - WAJIB verifikasi checksum setelah backup selesai
  - WAJIB jalankan backup sebelum setiap update sistem
  - JANGAN hapus backup yang masih dalam retensi tanpa konfirmasi
  - WAJIB jalankan backup dalam background job (Queue), bukan synchronous
  - JANGAN ekspos path backup di URL publik — gunakan signed download URL
  - WAJIB aktifkan maintenance mode saat restore
```

---

## Best Practice

1. **Off-site Backup** — Selalu punya minimal satu salinan di remote storage (S3/SFTP)
2. **Reguler Testing** — Uji restore backup secara berkala (minimal bulanan)
3. **Encrypted Storage** — Semua backup dienkripsi dengan AES-256
4. **3-2-1 Rule** — 3 salinan, 2 media berbeda, 1 offsite
5. **Background Processing** — Backup besar via Queue agar tidak memblokir UI
6. **Checksum Verification** — Selalu verifikasi integritas sebelum restore
7. **Retention Policy** — Automatis hapus backup lama untuk hemat storage
8. **Maintenance Mode** — Aktifkan saat restore untuk menghindari konflik data

---

## Checklist

### Implementasi
- [ ] `BackupService` dengan semua method
- [ ] `BackupCommand` artisan commands
- [ ] `BackupJob` untuk queue processing
- [ ] Database dump via mysqldump/pg_dump
- [ ] File zip dengan exclusion support
- [ ] Enkripsi AES-256
- [ ] Upload ke S3 + SFTP
- [ ] Restore database dan files
- [ ] Retention policy enforcement
- [ ] Tabel `backups`

### Penjadwalan
- [ ] Daily database backup (02:00)
- [ ] Weekly full backup (Minggu 01:00)
- [ ] Daily cleanup old backups (03:00)

### UI
- [ ] Dashboard dengan daftar backup
- [ ] Tombol backup manual
- [ ] Download backup
- [ ] Restore dengan konfirmasi
- [ ] Status backup real-time
- [ ] Storage usage indicator

### Testing
- [ ] Unit test BackupService
- [ ] Test database dump dan restore
- [ ] Test file backup dan restore
- [ ] Test enkripsi/dekripsi
- [ ] Test retention policy

---

## Roadmap

| Versi | Fitur | Status |
|-------|-------|--------|
| v1.0 | Full backup + Schedule + Local storage | Planned |
| v1.1 | S3 / Remote storage upload | Planned |
| v1.2 | Incremental backup | Planned |
| v1.3 | Backup encryption | Planned |
| v2.0 | Point-in-time recovery | Future |
| v2.1 | Cross-server restore | Future |
| v2.2 | Backup health monitoring dashboard | Future |

---

## Referensi

- [20_UPDATE_ENGINE.md](20_UPDATE_ENGINE.md) — Update Engine
- [26_QUEUE_ENGINE.md](26_QUEUE_ENGINE.md) — Queue Engine
- [15_NOTIFICATION_ENGINE.md](15_NOTIFICATION_ENGINE.md) — Notification Engine
- [25_LOG_ENGINE.md](25_LOG_ENGINE.md) — Log Engine
- [38_DEPLOYMENT.md](38_DEPLOYMENT.md) — Deployment Guide

---

*Dokumen ini adalah bagian dari CosmicLib Engine Documentation Suite.*
*Dibuat: 2026 | Terakhir diperbarui: 2026-07-19*