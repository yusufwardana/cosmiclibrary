# 25 — Log Engine

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

Log Engine menyediakan sistem pencatatan aktivitas (activity log), audit trail, dan error tracking yang komprehensif untuk seluruh CosmicLib. Engine ini memastikan setiap operasi penting tercatat untuk keperluan keamanan, debugging, dan audit.

**Fungsi utama:**
- Activity log — mencatat siapa melakukan apa dan kapan
- Audit trail — mencatat perubahan data (sebelum vs sesudah)
- System log — mencatat error, warning, dan info sistem
- Auth log — mencatat aktivitas login/logout/gagal
- Performance log — mencatat query lambat dan response time
- Log viewer — UI untuk membaca dan filter log
- Log rotation dan retention management

---

## Arsitektur

```
┌─────────────────────────────────────────────────────────────┐
│                      Log Engine                             │
│                                                             │
│  ┌──────────────────────────────────────────────────────┐  │
│  │              Log Channels                             │  │
│  │                                                       │  │
│  │  Activity Log ──▶ DB (activity_logs table)            │  │
│  │  Audit Log    ──▶ DB (audit_logs table)               │  │
│  │  Auth Log     ──▶ DB (auth_logs table)                │  │
│  │  System Log   ──▶ File (storage/logs/*.log)           │  │
│  │  Query Log    ──▶ File (slow queries)                 │  │
│  │  Error Log    ──▶ File + Notification (critical)      │  │
│  └──────────────────────────────────────────────────────┘  │
│                                                             │
│  ┌──────────────────────────────────────────────────────┐  │
│  │              Log Facade / Helper                      │  │
│  │                                                       │  │
│  │  ActivityLog::record(user, action, model, changes)    │  │
│  │  AuditLog::record(model, event, old, new)             │  │
│  │  Log::info() | Log::warning() | Log::error()          │  │
│  └──────────────────────────────────────────────────────┘  │
│                                                             │
│  ┌──────────────────────────────────────────────────────┐  │
│  │              Log Viewer (Admin UI)                    │  │
│  │                                                       │  │
│  │  Filter: type | user | date | level | search         │  │
│  └──────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────┘
```

---

## Komponen

### 1. Service — `ActivityLogService`

```
ActivityLogService
├── record(LogEntry $entry): ActivityLog
├── recordAction(User $user, string $action, Model $subject): ActivityLog
├── recordModelChange(Model $model, string $event, array $old, array $new): AuditLog
├── getByUser(User $user, array $filters): Collection
├── getByModel(Model $model): Collection
├── purgeOldLogs(int $daysOld): int
└── exportLogs(array $filters): Collection
```

### 2. Model — `ActivityLog`

```php
class ActivityLog extends Model
{
    protected $fillable = [
        'log_name',      // channel: activity | auth | admin
        'description',   // Deskripsi aksi
        'subject_type',  // Model class
        'subject_id',    // Model ID
        'causer_type',   // User class
        'causer_id',     // User ID
        'properties',    // JSON: { old, new, attributes }
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'properties' => 'array',
    ];
}
```

### 3. Model — `AuditLog`

```php
class AuditLog extends Model
{
    protected $fillable = [
        'user_id',
        'user_type',
        'event',          // created | updated | deleted | restored
        'auditable_type', // Model yang diubah
        'auditable_id',
        'old_values',     // JSON
        'new_values',     // JSON
        'url',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];
}
```

### 4. Trait — `Auditable`

```php
// Tambahkan ke model yang perlu diaudit
trait Auditable
{
    protected static function bootAuditable(): void
    {
        static::created(fn($model)  => AuditLog::recordCreated($model));
        static::updated(fn($model)  => AuditLog::recordUpdated($model));
        static::deleted(fn($model)  => AuditLog::recordDeleted($model));
    }

    public function audits(): MorphMany
    {
        return $this->morphMany(AuditLog::class, 'auditable');
    }
}
```

### 5. Database Tables

**Tabel `activity_logs`**
```sql
CREATE TABLE activity_logs (
    id           BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    log_name     VARCHAR(50) NULL,
    description  TEXT NOT NULL,
    subject_type VARCHAR(255) NULL,
    subject_id   BIGINT UNSIGNED NULL,
    causer_type  VARCHAR(255) NULL,
    causer_id    BIGINT UNSIGNED NULL,
    properties   JSON NULL,
    ip_address   VARCHAR(45) NULL,
    user_agent   TEXT NULL,
    created_at   TIMESTAMP NULL,
    updated_at   TIMESTAMP NULL,

    INDEX idx_al_log_name (log_name),
    INDEX idx_al_subject (subject_type, subject_id),
    INDEX idx_al_causer (causer_type, causer_id),
    INDEX idx_al_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**Tabel `audit_logs`**
```sql
CREATE TABLE audit_logs (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id         BIGINT UNSIGNED NULL,
    event           VARCHAR(50) NOT NULL,
    auditable_type  VARCHAR(255) NOT NULL,
    auditable_id    BIGINT UNSIGNED NOT NULL,
    old_values      JSON NULL,
    new_values      JSON NULL,
    url             TEXT NULL,
    ip_address      VARCHAR(45) NULL,
    user_agent      TEXT NULL,
    created_at      TIMESTAMP NULL,

    INDEX idx_audit_user (user_id),
    INDEX idx_audit_auditable (auditable_type, auditable_id),
    INDEX idx_audit_event (event),
    INDEX idx_audit_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 6. Log Levels dan Actions

| Level | Digunakan Untuk |
|-------|-----------------|
| `debug` | Informasi pengembangan |
| `info` | Operasi normal (login, CRUD sukses) |
| `notice` | Kejadian yang perlu diperhatikan |
| `warning` | Potensi masalah (login gagal, rate limit) |
| `error` | Error yang perlu penanganan |
| `critical` | Error kritis (DB down, disk penuh) → kirim notifikasi |
| `alert` | Perlu tindakan segera |
| `emergency` | Sistem tidak bisa berfungsi |

| Action | Contoh |
|--------|--------|
| `user.login` | User berhasil login |
| `user.logout` | User logout |
| `user.login_failed` | Login gagal |
| `book.created` | Buku baru ditambahkan |
| `loan.created` | Peminjaman dibuat |
| `loan.returned` | Buku dikembalikan |
| `fine.paid` | Denda dibayar |
| `setting.updated` | Pengaturan diubah |
| `backup.completed` | Backup selesai |

---

## Lifecycle

### Activity Logging

```php
// Otomatis via Observer atau Manual via Service
ActivityLog::record([
    'log_name'    => 'activity',
    'description' => 'Meminjam buku: Laravel in Action',
    'subject'     => $book,
    'causer'      => auth()->user(),
    'properties'  => ['loan_id' => 123, 'due_date' => '2026-08-01'],
]);
```

### Audit Logging

```php
// Otomatis via Auditable trait
// Saat Book::update(['title' => 'New Title'])
// AuditLog akan menyimpan:
// old_values: { "title": "Old Title" }
// new_values: { "title": "New Title" }
```

### Log Retention

```
Cron harian (03:30):
  └── php artisan log:purge
      ├── Hapus activity_logs > 365 hari
      ├── Hapus audit_logs > 730 hari (2 tahun)
      ├── Hapus auth_logs > 180 hari
      └── Rotate file logs (kompres log minggu lalu)
```

---

## Konfigurasi

### Laravel Logging Channel

```php
// config/logging.php
'channels' => [
    'stack' => [
        'driver'   => 'stack',
        'channels' => ['daily', 'slack'],
    ],
    'daily' => [
        'driver'    => 'daily',
        'path'      => storage_path('logs/cosmiclib.log'),
        'level'     => env('LOG_LEVEL', 'debug'),
        'days'      => 30,
    ],
    'slack' => [
        'driver'   => 'slack',
        'url'      => env('LOG_SLACK_WEBHOOK_URL'),
        'username' => 'CosmicLib Log',
        'emoji'    => ':warning:',
        'level'    => 'critical',
    ],
],
```

### Log Retention Configuration

```php
// config/log.php
return [
    'retention' => [
        'activity' => 365,   // hari
        'audit'    => 730,
        'auth'     => 180,
        'system'   => 30,
    ],
];
```

---

## Integrasi

### Dengan Auth Engine
- Auth Engine memanggil `ActivityLogService` untuk setiap event login/logout/gagal

### Dengan semua Module
- Semua Service menggunakan `ActivityLogService::record()` untuk aksi penting
- Model yang Auditable menggunakan trait `Auditable`

### Dengan Notification Engine
- Log level `critical` dan `alert` memicu notifikasi ke admin via email/Slack

### Dengan Queue Engine
- Log berat (bulk operations) dicatat via Queue job untuk tidak memblokir request

---

## AI Rules

```yaml
log_engine_rules:
  - WAJIB log semua operasi sensitif (login, ubah password, ubah setting, hapus data)
  - JANGAN log data sensitif (password, token, API key) dalam values log
  - WAJIB sediakan audit trail untuk setiap model yang menyimpan data kritis
  - JANGAN biarkan log tumbuh tanpa batas — wajib ada retention policy
  - WAJIB log IP address dan user agent untuk setiap aksi penting
  - JANGAN gunakan dd() atau dump() — gunakan Log::debug()
  - WAJIB kirim notifikasi ke admin untuk log level critical ke atas
  - JANGAN hapus log secara manual — gunakan retention policy otomatis
```

---

## Best Practice

1. **Structured Logging** — Log dalam format terstruktur (JSON) untuk kemudahan parsing
2. **Context Enrichment** — Sertakan user ID, IP, request ID di setiap log
3. **Log Rotation** — File log dirotasi dan dikompresi harian
4. **Masking Sensitive Data** — Sembunyikan field sensitif sebelum log
5. **Centralized Viewing** — Sediakan Log Viewer di admin panel
6. **Alerting** — Critical logs → notifikasi real-time
7. **Performance** — Log asinkron via Queue untuk operasi berat

---

## Checklist

### Implementasi
- [ ] `ActivityLogService` dengan semua method
- [ ] Model `ActivityLog` dan `AuditLog`
- [ ] Trait `Auditable` untuk model
- [ ] Tabel `activity_logs` dan `audit_logs`
- [ ] Log retention command
- [ ] Slack/email channel untuk critical logs
- [ ] Log Viewer controller + view

### Model yang Perlu Auditable
- [ ] Book (Auditable)
- [ ] Member (Auditable)
- [ ] Loan (Auditable)
- [ ] Setting (Auditable)
- [ ] User (Auditable)

### Testing
- [ ] Unit test ActivityLogService
- [ ] Test Auditable trait
- [ ] Test log retention
- [ ] Test masking sensitive data

---

## Roadmap

| Versi | Fitur | Status |
|-------|-------|--------|
| v1.0 | Activity Log + Audit Log + DB storage | Planned |
| v1.1 | Log Viewer UI dengan filter | Planned |
| v1.2 | Log retention otomatis | Planned |
| v2.0 | ELK Stack integration | Future |
| v2.1 | Real-time log streaming | Future |
| v2.2 | AI anomaly detection dari log | Future |

---

## Referensi

- [17_AUTH_ENGINE.md](17_AUTH_ENGINE.md) — Auth Engine
- [26_QUEUE_ENGINE.md](26_QUEUE_ENGINE.md) — Queue Engine
- [15_NOTIFICATION_ENGINE.md](15_NOTIFICATION_ENGINE.md) — Notification Engine
- [42_SECURITY_GUIDE.md](42_SECURITY_GUIDE.md) — Security Guide

---

*Dokumen ini adalah bagian dari CosmicLib Engine Documentation Suite.*
*Dibuat: 2026 | Terakhir diperbarui: 2026-07-19*