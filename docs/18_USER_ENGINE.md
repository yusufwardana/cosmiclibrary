# 18 — User Engine

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

User Engine mengelola seluruh siklus hidup pengguna sistem CosmicLib — mulai dari registrasi, manajemen profil, pengelompokan user berdasarkan role, hingga penonaktifan akun. Engine ini bertindak sebagai **pusat data identitas** yang digunakan oleh semua engine lain.

**Fungsi utama:**
- Manajemen data pengguna (CRUD)
- Pengelolaan profil dan avatar
- Pengelompokan user berdasarkan tipe (admin, staff, member, guest)
- Manajemen role dan assignment ke user
- Riwayat aktivitas pengguna
- Export/import data pengguna
- Bulk operations (aktifkan/nonaktifkan massal)

---

## Arsitektur

```
┌─────────────────────────────────────────────────────────────┐
│                       User Engine                           │
│                                                             │
│  ┌────────────┐    ┌──────────────┐    ┌─────────────────┐  │
│  │  User UI   │───▶│ UserRequest  │───▶│  UserService    │  │
│  │  (Admin)   │    │ (Validation) │    │                 │  │
│  └────────────┘    └──────────────┘    └────────┬────────┘  │
│                                                 │           │
│  ┌──────────────────────────────────────────────┤           │
│  │                                              │           │
│  ▼                                              ▼           │
│  ┌─────────────┐    ┌──────────────┐    ┌──────────────┐   │
│  │ UserProfile │    │ RoleService  │    │ UserRepository│   │
│  │ (Avatar,    │    │ (Assign/     │    │ (DB Layer)   │   │
│  │  Settings)  │    │  Revoke)     │    └──────┬───────┘   │
│  └─────────────┘    └──────────────┘           │           │
│                                                 │           │
│                                         ┌───────▼───────┐  │
│                                         │  users table  │  │
│                                         │  user_roles   │  │
│                                         │  user_profiles│  │
│                                         └───────────────┘  │
└─────────────────────────────────────────────────────────────┘
```

### User Type Hierarchy

```
System
  └── Administrator (Super Admin)
        └── Admin (Kepala Pustakawan)
              └── Staff (Pustakawan)
                    └── Member (Anggota Perpustakaan)
                          └── Guest (Pengunjung Tamu)
```

---

## Komponen

### 1. Model — `User`

```php
// app/Models/User.php
namespace App\Models;

class User extends Authenticatable
{
    protected $fillable = [
        'name',
        'email',
        'password',
        'user_type',       // admin | staff | member | guest
        'is_active',
        'is_verified',
        'email_verified_at',
        'last_login_at',
        'last_login_ip',
        'login_attempts',
        'account_locked_until',
        'avatar',
        'phone',
        'locale',
        'timezone',
        'two_factor_enabled',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at'    => 'datetime',
        'last_login_at'        => 'datetime',
        'account_locked_until' => 'datetime',
        'is_active'            => 'boolean',
        'is_verified'          => 'boolean',
        'two_factor_enabled'   => 'boolean',
        'login_attempts'       => 'integer',
    ];

    // Relationships
    public function roles(): BelongsToMany { }
    public function permissions(): Collection { } // via roles
    public function profile(): HasOne { }
    public function member(): HasOne { }         // jika user_type = member
    public function activityLogs(): HasMany { }
    public function apiTokens(): HasMany { }
}
```

### 2. Model — `UserProfile`

```php
class UserProfile extends Model
{
    protected $fillable = [
        'user_id',
        'bio',
        'address',
        'city',
        'province',
        'postal_code',
        'country',
        'birth_date',
        'gender',         // male | female | other
        'occupation',
        'education',
        'social_facebook',
        'social_twitter',
        'social_instagram',
        'preferences',    // JSON
    ];
}
```

### 3. Service — `UserService`

```
UserService
├── findById(int $id): User
├── findByEmail(string $email): ?User
├── create(array $data): User
├── update(User $user, array $data): User
├── delete(User $user): bool
├── activate(User $user): bool
├── deactivate(User $user): bool
├── bulkActivate(array $ids): int
├── bulkDeactivate(array $ids): int
├── assignRole(User $user, Role $role): bool
├── revokeRole(User $user, Role $role): bool
├── syncRoles(User $user, array $roleIds): bool
├── updateAvatar(User $user, UploadedFile $file): string
├── updateProfile(User $user, array $data): UserProfile
├── getActivityHistory(User $user): Collection
├── exportUsers(array $filters): Collection
└── importUsers(array $data): ImportResult
```

### 4. Service — `UserProfileService`

```
UserProfileService
├── getOrCreate(User $user): UserProfile
├── updateProfile(UserProfile $profile, array $data): UserProfile
├── getPreferences(User $user): array
└── updatePreferences(User $user, array $prefs): bool
```

### 5. Repository — `UserRepository`

```
UserRepository
├── findById(int $id): ?User
├── findByEmail(string $email): ?User
├── paginate(array $filters, int $perPage): LengthAwarePaginator
├── create(array $data): User
├── update(User $user, array $data): User
├── delete(User $user): bool
├── countByType(string $type): int
└── search(string $query): Collection
```

### 6. Controller — `UserController` (Admin)

```
UserController
├── index(): View       // Daftar semua user
├── create(): View      // Form tambah user
├── store(Request): Redirect
├── show(User): View    // Detail user
├── edit(User): View    // Form edit user
├── update(Request, User): Redirect
├── destroy(User): Redirect
├── activate(User): Redirect
├── deactivate(User): Redirect
├── assignRole(Request, User): Redirect
└── exportCsv(): Response
```

### 7. Form Requests

| Request | Field Validasi |
|---------|----------------|
| `CreateUserRequest` | name, email (unique), password, user_type |
| `UpdateUserRequest` | name, email (unique ignore self), phone, locale |
| `UpdateProfileRequest` | bio, address, birth_date, gender |
| `AssignRoleRequest` | roles (array, exists:roles) |
| `ImportUserRequest` | file (csv, max:2MB) |

---

## Lifecycle

### User Creation Lifecycle

```
1. Admin mengisi form / API request
2. CreateUserRequest validasi input
3. UserService::create()
   ├── Hash password
   ├── Set default user_type
   ├── Create User record
   ├── Create UserProfile (kosong)
   ├── Assign default role
   ├── Send welcome email (jika enabled)
   └── Log::info('user.created', ['admin' => auth()->id()])
4. Return redirect ke detail user
```

### User Activation/Deactivation

```
1. Admin klik tombol Aktifkan/Nonaktifkan
2. UserService::activate() / deactivate()
   ├── Set is_active = true/false
   ├── Jika nonaktifkan: revoke semua token API
   ├── Jika nonaktifkan: terminate session
   └── Log perubahan status
3. Notifikasi ke user via email (opsional)
```

### Avatar Upload

```
1. User upload foto via form
2. Validasi: image, max:2MB, mimes:jpeg,png,webp
3. UserService::updateAvatar()
   ├── Hapus avatar lama
   ├── Resize ke 300x300px
   ├── Simpan ke storage/avatars/
   └── Update users.avatar path
```

---

## Konfigurasi

### Database Tables

**Tabel `users`**
```sql
CREATE TABLE users (
    id                    BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name                  VARCHAR(150) NOT NULL,
    email                 VARCHAR(255) NOT NULL UNIQUE,
    email_verified_at     TIMESTAMP NULL,
    password              VARCHAR(255) NOT NULL,
    user_type             ENUM('admin','staff','member','guest') DEFAULT 'guest',
    is_active             TINYINT(1) NOT NULL DEFAULT 1,
    is_verified           TINYINT(1) NOT NULL DEFAULT 0,
    avatar                VARCHAR(255) NULL,
    phone                 VARCHAR(20) NULL,
    locale                VARCHAR(10) DEFAULT 'id',
    timezone              VARCHAR(50) DEFAULT 'Asia/Jakarta',
    last_login_at         TIMESTAMP NULL,
    last_login_ip         VARCHAR(45) NULL,
    login_attempts        TINYINT UNSIGNED DEFAULT 0,
    account_locked_until  TIMESTAMP NULL,
    two_factor_enabled    TINYINT(1) DEFAULT 0,
    remember_token        VARCHAR(100) NULL,
    created_at            TIMESTAMP NULL,
    updated_at            TIMESTAMP NULL,
    deleted_at            TIMESTAMP NULL,

    INDEX idx_users_email (email),
    INDEX idx_users_type (user_type),
    INDEX idx_users_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**Tabel `user_profiles`**
```sql
CREATE TABLE user_profiles (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id     BIGINT UNSIGNED NOT NULL UNIQUE,
    bio         TEXT NULL,
    address     TEXT NULL,
    city        VARCHAR(100) NULL,
    province    VARCHAR(100) NULL,
    postal_code VARCHAR(10) NULL,
    country     VARCHAR(50) DEFAULT 'Indonesia',
    birth_date  DATE NULL,
    gender      ENUM('male','female','other') NULL,
    occupation  VARCHAR(100) NULL,
    education   VARCHAR(100) NULL,
    preferences JSON NULL,
    created_at  TIMESTAMP NULL,
    updated_at  TIMESTAMP NULL,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**Tabel `model_has_roles`** (Spatie Permission)
```sql
CREATE TABLE model_has_roles (
    role_id     BIGINT UNSIGNED NOT NULL,
    model_type  VARCHAR(255) NOT NULL,
    model_id    BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (role_id, model_id, model_type),
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### Konfigurasi User Types

```php
// config/user.php
return [
    'types' => [
        'admin'  => 'Administrator',
        'staff'  => 'Staff Pustakawan',
        'member' => 'Anggota',
        'guest'  => 'Tamu',
    ],
    'default_type'   => 'guest',
    'default_locale' => 'id',
    'avatar_disk'    => 'public',
    'avatar_path'    => 'avatars',
    'avatar_size'    => 300,
    'import_limit'   => 500,  // max rows per import
];
```

---

## Integrasi

### Dengan Auth Engine
- Model `User` adalah entitas utama untuk autentikasi
- Auth Engine menggunakan `User::is_active`, `login_attempts`, `account_locked_until`

### Dengan Permission Engine
- User memiliki roles melalui relasi `BelongsToMany`
- Permission Engine query `user->can()` melalui roles yang dimiliki user

### Dengan Member Module
- User dengan `user_type = 'member'` memiliki record di tabel `members`
- UserService::create() dengan type member → auto-create member record

### Dengan Notification Engine
```php
// Trigger notifikasi saat user dibuat
UserCreated::dispatch($user);

// Trigger notifikasi akun dinonaktifkan
UserDeactivated::dispatch($user);
```

### Dengan Log Engine
- Semua operasi CRUD user dicatat di activity log
- Format: `user.{created|updated|deleted|activated|deactivated}`

### Dengan Media Engine
- Avatar upload melalui Media Engine untuk resize dan storage
- Media Engine mengelola path dan URL avatar

---

## AI Rules

```yaml
user_engine_rules:
  - JANGAN simpan password dalam bentuk plain text
  - WAJIB gunakan soft delete (deleted_at) untuk user, JANGAN hard delete
  - JANGAN hapus user yang memiliki data terkait (loans, returns) — deactivate saja
  - WAJIB log semua perubahan data user (siapa yang mengubah, kapan, nilai apa)
  - JANGAN expose field sensitif (password, remember_token) via API
  - WAJIB validasi bahwa user yang dinonaktifkan tidak dapat login
  - JANGAN hardcode user_type — gunakan konstanta atau enum
  - WAJIB bungkus operasi sensitif dalam try-catch dan DB::transaction
```

---

## Best Practice

1. **Soft Delete** — Gunakan soft delete; data historis tetap terjaga
2. **Eager Loading** — Load `roles.permissions` saat user dibutuhkan untuk otorisasi
3. **Avatar Processing** — Resize otomatis saat upload, hapus file lama
4. **Data Minimization** — Hanya simpan data yang diperlukan (GDPR awareness)
5. **Bulk Operations** — Gunakan chunk untuk bulk import/export (hindari memory overflow)
6. **Audit Trail** — Setiap perubahan data user tercatat lengkap
7. **Search Optimization** — Index pada email, user_type, is_active
8. **Export Security** — Hanya admin yang bisa export data user (permission: users.export)

---

## Checklist

### Implementasi
- [ ] Model `User` dengan semua field dan relationships
- [ ] Model `UserProfile` dengan relasi ke `User`
- [ ] `UserService` dengan semua method
- [ ] `UserProfileService`
- [ ] `UserRepository` dengan paginate dan filter
- [ ] `UserController` admin panel
- [ ] Semua Form Request dengan validasi
- [ ] Avatar upload dan resize
- [ ] Bulk activate/deactivate
- [ ] Import/Export CSV

### Database
- [ ] Tabel `users` dengan semua kolom
- [ ] Tabel `user_profiles`
- [ ] Tabel `model_has_roles`
- [ ] Index yang tepat
- [ ] Soft delete support

### UI Admin
- [ ] Daftar user dengan filter dan search
- [ ] Form tambah/edit user
- [ ] Upload avatar dengan preview
- [ ] Toggle status aktif/nonaktif
- [ ] Assign/revoke role
- [ ] Detail riwayat aktivitas user
- [ ] Halaman profile user sendiri

### Testing
- [ ] Unit test UserService
- [ ] Feature test CRUD user
- [ ] Feature test bulk operations
- [ ] Test avatar upload
- [ ] Test import/export

---

## Roadmap

| Versi | Fitur | Status |
|-------|-------|--------|
| v1.0 | User CRUD + Profile + Avatar | Planned |
| v1.1 | Import/Export CSV | Planned |
| v1.2 | Bulk Operations | Planned |
| v1.3 | User Activity Log | Planned |
| v2.0 | User Groups / Department | Future |
| v2.1 | GDPR Data Export (self-service) | Future |
| v2.2 | User Merge (duplicate handling) | Future |

---

## Referensi

- [17_AUTH_ENGINE.md](17_AUTH_ENGINE.md) — Auth Engine
- [10_PERMISSION_ENGINE.md](10_PERMISSION_ENGINE.md) — Permission Engine
- [29_MEMBER_MODULE.md](29_MEMBER_MODULE.md) — Member Module
- [14_MEDIA_ENGINE.md](14_MEDIA_ENGINE.md) — Media Engine
- [15_NOTIFICATION_ENGINE.md](15_NOTIFICATION_ENGINE.md) — Notification Engine

---

*Dokumen ini adalah bagian dari CosmicLib Engine Documentation Suite.*
*Dibuat: 2026 | Terakhir diperbarui: 2026-07-19*