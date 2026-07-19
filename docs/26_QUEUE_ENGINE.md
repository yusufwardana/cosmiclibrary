# 26 — Queue Engine

> **CosmicLib Engine Documentation** | Versi: 1.0.0 | Status: Blueprint

---

## Daftar Isi

1. [Tujuan](#tujuan) · 2. [Arsitektur](#arsitektur) · 3. [Komponen](#komponen) · 4. [Lifecycle](#lifecycle) · 5. [Konfigurasi](#konfigurasi) · 6. [Integrasi](#integrasi) · 7. [AI Rules](#ai-rules) · 8. [Best Practice](#best-practice) · 9. [Checklist](#checklist) · 10. [Roadmap](#roadmap)

---

## Tujuan

Queue Engine mengelola **pekerjaan asinkron** CosmicLib. Engine ini menentukan driver queue yang sesuai berdasarkan infrastruktur (file-based untuk shared hosting, Redis/database untuk VPS), menyediakan abstraction layer untuk dispatch Job, dan memastikan semua operasi berat tidak memblokir request HTTP.

**Fungsi utama:**
- Dispatch dan process job via queue driver yang configurable
- Retry mechanism dengan exponential backoff
- Job chaining dan batch processing
- Failed job management dan monitoring
- Scheduler integration untuk cron-like tasks
- Shared hosting fallback (sync + database queue)

---

## Arsitektur

```
┌─────────────────────────────────────────────────────┐
│                 Queue Engine                         │
│                                                     │
│  Dispatch ──▶ Queue Driver ──▶ Job Handler          │
│                  │                                   │
│      ┌───────────┼───────────┐                       │
│      ▼           ▼           ▼                       │
│   database    redis       sync (dev)                │
│  (shared     (VPS)      (fallback)                  │
│   hosting)                                           │
│                                                     │
│  ┌─────────────┐  ┌────────────┐  ┌──────────────┐ │
│  │ Job Classes │  │ Scheduler  │  │ Failed Jobs  │ │
│  │ (per modul) │  │ (Artisan)  │  │ Table        │ │
│  └─────────────┘  └────────────┘  └──────────────┘ │
└─────────────────────────────────────────────────────┘
```

---

## Komponen

### 1. Job Classes (Contoh)

| Job | Module | Fungsi |
|-----|--------|--------|
| `IndexBookJob` | Book | Index buku ke Search Engine |
| `SendEmailJob` | Notification | Kirim email async |
| `BackupJob` | Backup | Jalankan backup database |
| `ExportReportJob` | Report | Generate laporan PDF/CSV |
| `ImportMemberJob` | Member | Import data anggota dari CSV |
| `SendOverdueNotifJob` | Loan | Kirim notifikasi keterlambatan |
| `CleanupExpiredTokensJob` | Auth | Bersihkan token expired |

### 2. Driver Strategy

| Driver | Cocok Untuk | Kelebihan |
|--------|-------------|-----------|
| `database` | Shared Hosting cPanel | Tidak perlu service tambahan |
| `redis` | VPS / Cloud | Performa tinggi, reliable |
| `sync` | Development | Synchronous, debugging mudah |

---

## Lifecycle

### Job Dispatch & Processing

```
1. Service::doSomething()
   └── dispatch(new ImportantJob($data))  // Async

2. Queue Worker picks up job
   ├── Deserialize job class
   ├── Resolve dependencies via Container
   ├── Execute handle() method
   │   ├── [Success] Mark as completed
   │   ├── [Failed, retries < max] Re-queue with backoff
   │   └── [Failed, retries exhausted] Move to failed_jobs
   └── Log execution
```

### Scheduler Lifecycle

```
┌──────────────────────────────────┐
│  Laravel Scheduler (cron/minute) │
├──────────────────────────────────┤
│                                  │
│  * * * * * → schedule:run        │
│    │                             │
│    ├── Backup Job (daily 02:00)  │
│    ├── Cleanup Job (daily 03:00) │
│    ├── Overdue Notif (daily 08:00)│
│    ├── Report Job (weekly Sun)   │
│    └── Token Cleanup (daily)     │
│                                  │
└──────────────────────────────────┘
```

---

## Konfigurasi

### Queue Connection

```php
// config/queue.php
'connections' => [
    'database' => [
        'driver' => 'database',
        'table'  => 'jobs',
        'queue'  => 'default',
        'retry_after' => 90,
    ],
    'redis' => [
        'driver' => 'redis',
        'connection' => 'default',
        'queue' => 'cosmiclib',
        'retry_after' => 90,
    ],
],
```

### Shared Hosting Fallback

```php
// .env (shared hosting)
QUEUE_CONNECTION=database

// Perintah manual atau scheduler
php artisan queue:work --sleep=3 --tries=3
```

### Environment Variables

```env
QUEUE_CONNECTION=database
BROADCAST_DRIVER=log
QUEUE_FAILED_DRIVER=database-uuids
QUEUE_RETRY_AFTER=90
```

---

## Integrasi

### Dengan Backup Engine
- `BackupJob` dispatched via queue untuk backup harian/mingguan

### Dengan Notification Engine
- `SendEmailJob` mengirim email secara async
- `SendOverdueNotifJob` mengirim peringatan keterlambatan

### Dengan Search Engine
- `IndexBookJob` dan `ReindexSearchJob` menjalankan indexing async

### Dengan Media Engine
- `ProcessMediaJob` resize/kompres file upload

### Dengan Log Engine
- Semua job execution dicatat di activity log

---

## AI Rules

```yaml
queue_engine_rules:
  - WAJIB implementasikan interface ShouldQueue untuk job async
  - JANGAN dispatch job sync di production — gunakan queue
  - WAJIB tentukan queue name yang spesifik per job
  - JANGAN simpan data besar di job properties — gunakan ID
  - WAJIB implementasikan retry logic dengan backoff
  - JANGAN catch exception tanpa re-throw — biarkan failed job system menangani
  - WAJIB test job secara independen dengan Queue::fake()
  - JANGAN gunakan queue untuk operasi yang harus selesai sebelum response dikirim
```

---

## Best Practice

1. **Queue Separation** — Gunakan queue berbeda per kategori (emails, backups, reports)
2. **Idempotent Jobs** — Pastikan job aman dijalankan ulang
3. **Chunked Processing** — Untuk bulk operations, gunakan `Bus::batch()`
4. **Progress Tracking** — Gunakan `SynchronizesWithProgresBar` untuk UI feedback
5. **Timeout** — Tentukan `timeout` property untuk menghindari job stuck
6. **Retry Strategy** — Gunakan `backoff()` untuk exponential delay

---

## Checklist

- [ ] Queue driver dikonfigurasi (database/redis)
- [ ] Semua Job classes dibuat dan terdaftar
- [ ] Failed jobs table dibuat
- [ ] Scheduler dikonfigurasi untuk semua periodic tasks
- [ ] Queue worker daemon untuk production
- [ ] Monitoring failed jobs

---

## Roadmap

| Versi | Fitur | Status |
|-------|-------|--------|
| v1.0 | Database queue + Scheduler | Planned |
| v1.1 | Redis queue + Job batching | Planned |
| v2.0 | Horizon dashboard (monitoring) | Future |
| v2.1 | Distributed queue (multi-server) | Future |

---

## Referensi

- [07_CORE_ENGINE.md](07_CORE_ENGINE.md) — Core Engine
- [21_BACKUP_ENGINE.md](21_BACKUP_ENGINE.md) — Backup Engine
- [15_NOTIFICATION_ENGINE.md](15_NOTIFICATION_ENGINE.md) — Notification Engine

---

*Dokumen ini adalah bagian dari CosmicLib Engine Documentation Suite.*