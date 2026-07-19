# 22 — License Engine

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

License Engine mengelola sistem lisensi perangkat lunak CosmicLib. Engine ini memastikan bahwa setiap instalasi CosmicLib memiliki lisensi yang valid, mengontrol fitur yang tersedia berdasarkan tipe lisensi, dan mencegah penggunaan tidak sah melalui validasi kriptografis.

**Fungsi utama:**
- Aktivasi lisensi via license key
- Validasi lisensi (lokal + remote)
- Kontrol fitur berdasarkan tipe lisensi
- Deaktivasi dan pemindahan lisensi
- Peringatan masa berlaku lisensi
- Integrasi dengan Update Engine (update hanya untuk lisensi valid)

---

## Arsitektur

```
┌─────────────────────────────────────────────────────────────┐
│                    License Engine                           │
│                                                             │
│  ┌──────────────────────────────────────────────────────┐  │
│  │              License Validation Flow                  │  │
│  │                                                       │  │
│  │  App Request ──▶ LicenseMiddleware ──▶ LicenseService │  │
│  │                                            │         │  │
│  │                               ┌────────────┤         │  │
│  │                               ▼            ▼         │  │
│  │                        Local Cache    Remote API     │  │
│  │                        (valid?)       (verify)       │  │
│  │                               └────────────┘         │  │
│  │                                    │                 │  │
│  │                             [Valid] │ [Invalid]       │  │
│  │                                    ▼        ▼        │  │
│  │                             Allow        Restrict    │  │
│  │                             Access       + Notify    │  │
│  └──────────────────────────────────────────────────────┘  │
│                                                             │
│  ┌──────────────────────────────────────────────────────┐  │
│  │            License Types & Features                   │  │
│  │                                                       │  │
│  │  Community │ Professional │ Enterprise │ Trial        │  │
│  │  (Free)    │ (Berbayar)   │ (Korporat) │ (30 hari)   │  │
│  └──────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────┘
```

---

## Komponen

### 1. Model — `License`

```php
class License extends Model
{
    protected $fillable = [
        'license_key',
        'type',            // community | professional | enterprise | trial
        'domain',          // Domain yang diizinkan
        'max_users',       // Maksimum user
        'max_members',     // Maksimum anggota perpustakaan
        'features',        // JSON: fitur yang diaktifkan
        'issued_to',       // Nama pemegang lisensi
        'issued_at',       // Tanggal penerbitan
        'expires_at',      // Tanggal kedaluwarsa
        'activated_at',    // Tanggal aktivasi
        'status',          // active | expired | suspended | invalid
        'last_verified_at',
        'activation_count',
        'max_activations',
    ];

    protected $casts = [
        'features'         => 'array',
        'issued_at'        => 'datetime',
        'expires_at'       => 'datetime',
        'activated_at'     => 'datetime',
        'last_verified_at' => 'datetime',
    ];
}
```

### 2. Service — `LicenseService`

```
LicenseService
├── activate(string $key): LicenseResult
├── deactivate(): bool
├── validate(): LicenseStatus
├── verifyWithServer(): bool
├── isExpired(): bool
├── hasFeature(string $feature): bool
├── getLicenseType(): string
├── getDaysUntilExpiry(): int
├── getActiveLicense(): ?License
└── getLicenseInfo(): array
```

### 3. Middleware — `LicenseCheck`

```php
class LicenseCheck
{
    public function handle(Request $request, Closure $next, string $feature = null)
    {
        $license = app(LicenseService::class);

        if (!$license->validate()->isValid()) {
            return redirect()->route('license.expired');
        }

        if ($feature && !$license->hasFeature($feature)) {
            abort(403, 'Fitur ini memerlukan lisensi yang lebih tinggi.');
        }

        return $next($request);
    }
}
```

### 4. License Types

| Type | Fitur | Max Users | Max Anggota | Harga |
|------|-------|-----------|-------------|-------|
| Community | Core + Library dasar | 5 | 500 | Gratis |
| Professional | + Digital Library + API + CMS | 25 | 5.000 | Berbayar |
| Enterprise | Semua fitur + Multi-tenant | Unlimited | Unlimited | Korporat |
| Trial | Professional (30 hari) | 10 | 1.000 | Gratis |

### 5. Feature Flags

```php
// Fitur yang dikontrol lisensi
const FEATURES = [
    'digital_library'    => ['professional', 'enterprise'],
    'api_access'         => ['professional', 'enterprise'],
    'cms'                => ['professional', 'enterprise'],
    'multi_tenant'       => ['enterprise'],
    'advanced_reports'   => ['professional', 'enterprise'],
    'bulk_import'        => ['professional', 'enterprise'],
    'custom_themes'      => ['professional', 'enterprise'],
    'sso'                => ['enterprise'],
    'ldap'               => ['enterprise'],
    'audit_log'          => ['professional', 'enterprise'],
];
```

---

## Lifecycle

### Aktivasi Lisensi

```
1. Admin buka halaman License Engine
2. Input license key
3. LicenseService::activate(key)
4. Kirim request ke license.cosmiclib.id/api/activate
   ├── Payload: { key, domain, server_ip, version }
   ├── [GAGAL] Return error message
   └── [SUKSES] Terima license data (type, features, expires_at)
5. Simpan ke tabel licenses + file cache
6. Cache lisensi (Redis/file, TTL 24 jam)
7. Tampilkan info lisensi aktif
```

### Validasi Periodik

```
Setiap request ke halaman sensitif:
  └── LicenseMiddleware::handle()
      └── LicenseService::validate()
          ├── Check local cache (TTL 24 jam)
          │   └── [HIT, valid] Allow
          └── [MISS or expired cache]
              └── LicenseService::verifyWithServer()
                  ├── [Server unreachable] Use grace period (7 hari)
                  ├── [License invalid] Restrict access
                  └── [License valid] Update cache, allow
```

### Expiry Handling

```
7 hari sebelum expired:
  └── Notifikasi email ke admin (daily reminder)

Hari H expired:
  └── Status = 'expired'
  └── Akses dibatasi ke fitur Community saja
  └── Banner peringatan di seluruh halaman

Setelah expired:
  └── Redirect ke halaman license expiry jika fitur berbayar diakses
```

---

## Konfigurasi

### Database Table — `licenses`

```sql
CREATE TABLE licenses (
    id               BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    license_key      VARCHAR(255) NOT NULL UNIQUE,
    type             ENUM('community','professional','enterprise','trial') NOT NULL,
    domain           VARCHAR(255) NULL,
    max_users        INT NULL,
    max_members      INT NULL,
    features         JSON NULL,
    issued_to        VARCHAR(255) NULL,
    issued_at        TIMESTAMP NULL,
    expires_at       TIMESTAMP NULL,
    activated_at     TIMESTAMP NULL,
    status           ENUM('active','expired','suspended','invalid') DEFAULT 'active',
    last_verified_at TIMESTAMP NULL,
    activation_count INT DEFAULT 0,
    max_activations  INT DEFAULT 1,
    created_at       TIMESTAMP NULL,
    updated_at       TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### Environment Variables

```env
LICENSE_SERVER_URL=https://license.cosmiclib.id/api
LICENSE_CACHE_TTL=86400      # 24 jam
LICENSE_GRACE_PERIOD=604800  # 7 hari
LICENSE_KEY=                 # Diisi saat aktivasi
```

---

## Integrasi

### Dengan Core Engine
- License Engine diinisialisasi saat boot, sebelum module lain
- Core Engine tidak akan load fitur berbayar tanpa lisensi valid

### Dengan Update Engine
- Update server memvalidasi lisensi sebelum mengirim paket update

### Dengan Notification Engine
- Notifikasi expiry 30, 14, 7, 3, 1 hari sebelum kedaluwarsa

### Dengan Menu Engine
- Menu item untuk fitur berbayar disembunyikan jika tidak berlisensi

### Dengan Permission Engine
- Permission tertentu hanya bisa di-assign jika lisensi mendukung fitur tersebut

---

## AI Rules

```yaml
license_engine_rules:
  - JANGAN bypass license check dalam production code
  - JANGAN simpan license key sebagai plain text di source code
  - WAJIB validasi lisensi secara periodik (minimal 24 jam sekali)
  - WAJIB sediakan grace period jika license server tidak dapat dijangkau
  - JANGAN block seluruh aplikasi jika license server down — gunakan cached license
  - WAJIB enkripsi data lisensi yang disimpan lokal
  - JANGAN hardcode fitur list — selalu baca dari license.features JSON
```

---

## Best Practice

1. **Graceful Degradation** — Jika server lisensi tidak tersedia, gunakan cache dengan grace period
2. **Offline Support** — Validasi lokal harus bisa berjalan tanpa internet (untuk jaringan tertutup)
3. **Clear Messaging** — Tampilkan informasi jelas tentang fitur yang memerlukan upgrade
4. **Secure Storage** — Encrypt data lisensi lokal
5. **Expiry Warning** — Berikan peringatan jauh-jauh hari sebelum lisensi habis

---

## Checklist

### Implementasi
- [ ] Model `License`
- [ ] `LicenseService` dengan aktivasi dan validasi
- [ ] `LicenseMiddleware`
- [ ] Feature flag system
- [ ] Remote validation dengan grace period
- [ ] Expiry detection dan notifikasi
- [ ] Tabel `licenses`

### UI
- [ ] Halaman info lisensi
- [ ] Form aktivasi lisensi
- [ ] Status lisensi di dashboard
- [ ] Banner peringatan expiry
- [ ] Halaman fitur terkunci (upgrade CTA)

### Testing
- [ ] Unit test LicenseService
- [ ] Test aktivasi dan deaktivasi
- [ ] Test feature flag check
- [ ] Test grace period behavior
- [ ] Test expiry handling

---

## Roadmap

| Versi | Fitur | Status |
|-------|-------|--------|
| v1.0 | Aktivasi + validasi online | Planned |
| v1.1 | Offline validation (airgapped) | Planned |
| v1.2 | Multi-site license | Planned |
| v2.0 | Subscription-based licensing | Future |
| v2.1 | Usage-based metering | Future |

---

## Referensi

- [20_UPDATE_ENGINE.md](20_UPDATE_ENGINE.md) — Update Engine
- [15_NOTIFICATION_ENGINE.md](15_NOTIFICATION_ENGINE.md) — Notification Engine
- [11_MENU_ENGINE.md](11_MENU_ENGINE.md) — Menu Engine

---

*Dokumen ini adalah bagian dari CosmicLib Engine Documentation Suite.*
*Dibuat: 2026 | Terakhir diperbarui: 2026-07-19*