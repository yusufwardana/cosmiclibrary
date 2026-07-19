# 20 — Update Engine

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

Update Engine mengelola proses **pembaruan sistem CosmicLib** secara aman dan terkontrol. Engine ini memungkinkan administrator melakukan update aplikasi — termasuk migrasi database, pembaruan aset, dan patch keamanan — melalui antarmuka web tanpa downtime yang tidak perlu.

**Fungsi utama:**
- Deteksi versi terbaru dari update server
- Unduh dan verifikasi paket update
- Backup otomatis sebelum update dijalankan
- Jalankan migrasi database baru
- Publikasi aset baru (CSS, JS, images)
- Rollback jika update gagal
- Notifikasi admin tentang ketersediaan update
- Riwayat update yang telah dijalankan

---

## Arsitektur

```
┌─────────────────────────────────────────────────────────────┐
│                     Update Engine                           │
│                                                             │
│  ┌──────────────────────────────────────────────────────┐  │
│  │              Update Workflow                          │  │
│  │                                                       │  │
│  │  Check ──▶ Download ──▶ Verify ──▶ Backup ──▶ Apply  │  │
│  │    │                                         │       │  │
│  │    │                              ┌──────────▼──────┐│  │
│  │    │                              │   Migrations    ││  │
│  │    │                              │   + Assets      ││  │
│  │    │                              └──────────┬──────┘│  │
│  │    │                                         │       │  │
│  │    │                              ┌──────────▼──────┐│  │
│  │    │                              │ Verify Success  ││  │
│  │    │                              │ or Rollback     ││  │
│  │    ▼                              └─────────────────┘│  │
│  │  [No update] ◀── Update available? ──▶ [Notify Admin] │  │
│  └──────────────────────────────────────────────────────┘  │
│                                                             │
│  ┌─────────────┐    ┌──────────────┐    ┌───────────────┐  │
│  │  Version    │    │   Package    │    │  Update Log   │  │
│  │  Checker    │    │  Downloader  │    │  (History)    │  │
│  └─────────────┘    └──────────────┘    └───────────────┘  │
└─────────────────────────────────────────────────────────────┘
```

---

## Komponen

### 1. Controller — `UpdateController`

```
UpdateController
├── index(): View              // Halaman manajemen update
├── checkForUpdates(): JsonResponse  // Cek update terbaru
├── download(): JsonResponse   // Unduh paket update
├── preview(): View            // Preview changelog
├── apply(): JsonResponse      // Terapkan update
├── rollback(id): JsonResponse // Rollback ke versi sebelumnya
└── history(): View            // Riwayat update
```

### 2. Service — `UpdateService`

```
UpdateService
├── checkAvailable(): UpdateInfo|null
├── getCurrentVersion(): string
├── getLatestVersion(): string
├── downloadPackage(string $version): string  // Return path
├── verifyPackage(string $path): bool         // Checksum verify
├── extractPackage(string $path): bool
├── runMigrations(): bool
├── publishAssets(): bool
├── applyUpdate(string $version): UpdateResult
├── rollback(int $updateId): bool
├── getHistory(): Collection
└── cleanupTempFiles(): void
```

### 3. Service — `VersionChecker`

```
VersionChecker
├── getCurrentVersion(): string    // Dari config/app.php atau composer.json
├── getLatestVersion(): string     // Dari update server API
├── hasUpdate(): bool
├── compareVersions(string $current, string $latest): int
└── getChangelog(string $version): string
```

### 4. Database Table — `update_logs`

```sql
CREATE TABLE update_logs (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    from_version    VARCHAR(20) NOT NULL,
    to_version      VARCHAR(20) NOT NULL,
    status          ENUM('pending','running','success','failed','rolled_back'),
    started_at      TIMESTAMP NULL,
    finished_at     TIMESTAMP NULL,
    performed_by    BIGINT UNSIGNED NULL,
    backup_path     VARCHAR(255) NULL,
    notes           TEXT NULL,
    error_message   TEXT NULL,
    created_at      TIMESTAMP NULL,
    updated_at      TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

---

## Lifecycle

### Update Lifecycle

```
1. Admin buka halaman Update Engine
2. UpdateService::checkAvailable()
   ├── Kirim request ke update.cosmiclib.id/api/latest
   ├── Bandingkan versi saat ini vs terbaru
   └── Return UpdateInfo (version, changelog, size, checksum)

3. Admin klik [Unduh Update]
   ├── UpdateService::downloadPackage()
   ├── Simpan ke storage/updates/{version}.zip
   └── UpdateService::verifyPackage() → checksum SHA256

4. Admin klik [Terapkan Update]
   ├── BackupService::createFullBackup() (otomatis)
   ├── Catat update_logs status = 'running'
   ├── UpdateService::extractPackage()
   ├── UpdateService::runMigrations()
   ├── UpdateService::publishAssets()
   ├── Cache::flush()
   ├── Catat update_logs status = 'success'
   └── Notifikasi sukses ke admin

5. [Jika gagal]
   ├── Catat error ke update_logs
   ├── UpdateService::rollback() → restore dari backup
   └── Notifikasi gagal ke admin
```

---

## Konfigurasi

### Environment Variables

```env
UPDATE_SERVER_URL=https://update.cosmiclib.id/api
UPDATE_CHECK_INTERVAL=86400  # detik (24 jam)
UPDATE_AUTO_BACKUP=true
UPDATE_TEMP_PATH=storage/updates
UPDATE_CHANNEL=stable  # stable | beta | nightly
```

### Config File

```php
// config/update.php
return [
    'server_url'       => env('UPDATE_SERVER_URL'),
    'check_interval'   => env('UPDATE_CHECK_INTERVAL', 86400),
    'auto_backup'      => env('UPDATE_AUTO_BACKUP', true),
    'temp_path'        => storage_path('updates'),
    'channel'          => env('UPDATE_CHANNEL', 'stable'),
    'timeout'          => 120,  // detik untuk download
    'verify_checksum'  => true,
];
```

---

## Integrasi

### Dengan Backup Engine
- Update Engine **selalu** menjalankan backup sebelum update diterapkan
- Backup path disimpan di `update_logs.backup_path` untuk rollback

### Dengan Notification Engine
- Notifikasi ke admin jika update tersedia (via email/dashboard)
- Notifikasi sukses/gagal setelah update dijalankan

### Dengan Log Engine
- Semua langkah update dicatat di activity log
- Error detail disimpan di `update_logs.error_message`

### Dengan Setting Engine
- `setting('update.auto_check')` menentukan apakah cek otomatis aktif
- `setting('update.last_check')` menyimpan timestamp pengecekan terakhir

### Dengan License Engine
- Update server memvalidasi lisensi sebelum memberikan paket update
- Hanya lisensi valid yang bisa mendapatkan update resmi

---

## AI Rules

```yaml
update_engine_rules:
  - WAJIB backup sebelum setiap update — JANGAN skip backup
  - WAJIB verifikasi checksum/signature paket update sebelum eksekusi
  - JANGAN jalankan update tanpa konfirmasi eksplisit dari admin
  - WAJIB rollback otomatis jika update gagal di tengah proses
  - JANGAN hapus backup update hingga update terbukti berhasil
  - WAJIB log setiap langkah proses update
  - JANGAN izinkan update saat ada job penting yang sedang berjalan di queue
  - WAJIB set maintenance mode saat menjalankan update
```

---

## Best Practice

1. **Maintenance Mode** — Aktifkan maintenance mode sebelum update dimulai
2. **Atomic Updates** — Seluruh proses update dalam satu transaksi; rollback total jika gagal
3. **Incremental Migration** — Pastikan setiap migrasi idempotent (aman dijalankan ulang)
4. **Checksum Verification** — Verifikasi SHA256 sebelum ekstrak paket
5. **Changelog Preview** — Tampilkan changelog sebelum admin memutuskan update
6. **Dry Run** — Sediakan mode simulasi update tanpa mengubah data
7. **Update Window** — Rekomendasikan waktu update di luar jam sibuk

---

## Checklist

### Implementasi
- [ ] `UpdateController` dengan semua endpoint
- [ ] `UpdateService` dengan full lifecycle
- [ ] `VersionChecker` dengan remote API call
- [ ] Package downloader dengan progress tracking
- [ ] Checksum verifier
- [ ] Migration runner
- [ ] Rollback mechanism
- [ ] Tabel `update_logs`

### UI
- [ ] Dashboard update dengan versi saat ini vs terbaru
- [ ] Progress bar unduh dan instalasi
- [ ] Preview changelog
- [ ] Riwayat update
- [ ] Tombol rollback dengan konfirmasi

### Testing
- [ ] Unit test VersionChecker
- [ ] Feature test apply update (mock)
- [ ] Test rollback mechanism
- [ ] Test checksum verification

---

## Roadmap

| Versi | Fitur | Status |
|-------|-------|--------|
| v1.0 | Manual update check + apply | Planned |
| v1.1 | Scheduled auto-check | Planned |
| v1.2 | Delta/patch updates (bukan full package) | Planned |
| v2.0 | Zero-downtime rolling update | Future |
| v2.1 | Module-specific partial updates | Future |

---

## Referensi

- [21_BACKUP_ENGINE.md](21_BACKUP_ENGINE.md) — Backup Engine
- [22_LICENSE_ENGINE.md](22_LICENSE_ENGINE.md) — License Engine
- [16_SYSTEM_SETTING_ENGINE.md](16_SYSTEM_SETTING_ENGINE.md) — Setting Engine
- [25_LOG_ENGINE.md](25_LOG_ENGINE.md) — Log Engine

---

*Dokumen ini adalah bagian dari CosmicLib Engine Documentation Suite.*
*Dibuat: 2026 | Terakhir diperbarui: 2026-07-19*