# 17 — Auth Engine

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

Auth Engine adalah sistem autentikasi dan otorisasi terpusat untuk CosmicLib. Engine ini menangani seluruh siklus identitas pengguna — dari login hingga logout — dengan dukungan multi-strategi autentikasi, proteksi brute-force, serta integrasi dengan Permission Engine.

**Fungsi utama:**
- Autentikasi pengguna (login/logout/register)
- Manajemen sesi dan token (session-based dan API token)
- Two-Factor Authentication (2FA) via TOTP
- Password management (reset, change, policy enforcement)
- Brute-force protection dan account lockout
- SSO integration (OAuth2 / LDAP — opsional)
- Audit log seluruh aktivitas autentikasi

---

## Arsitektur

```
┌─────────────────────────────────────────────────────────────┐
│                       Auth Engine                           │
│                                                             │
│  ┌────────────┐    ┌──────────────┐    ┌─────────────────┐  │
│  │ Login Form │───▶│ LoginRequest │───▶│  AuthService    │  │
│  │ API Login  │    │ (Validation) │    │                 │  │
│  └────────────┘    └──────────────┘    └────────┬────────┘  │
│                                                 │           │
│         ┌───────────────────────────────────────┤           │
│         │                                       │           │
│  ┌──────▼──────┐  ┌──────────────┐  ┌──────────▼────────┐  │
│  │  Guard      │  │ 2FA Service  │  │  Token Service    │  │
│  │ (Session/   │  │ (TOTP/Email) │  │  (Sanctum/JWT)    │  │
│  │  Token)     │  └──────────────┘  └──────────┬────────┘  │
│  └──────┬──────┘                               │           │
│         │                          ┌───────────▼────────┐  │
│  ┌──────▼──────┐                   │  personal_access   │  │
│  │  Auth Log   │                   │  _tokens table     │  │
│  │  (Events)   │                   └────────────────────┘  │
│  └─────────────┘                                           │
│                                                             │
│  ┌─────────────────────────────────────────────────────┐   │
│  │              Auth Strategies                         │   │
│  │  Local | API Token | OAuth2 | LDAP | 2FA            │   │
│  └─────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────┘
```

### Auth Flow — Login

```
User Input
    │
    ▼
LoginRequest::validate()
    │
    ▼
RateLimiter::check() ──[exceeded]──▶ Return 429 Too Many Requests
    │
    ▼
AuthService::attempt(credentials)
    │
    ├──[failed]──▶ Increment login_attempts ──▶ Log::warning
    │                    │
    │              [max_attempts]──▶ lockAccount() ──▶ Notify
    │
    └──[success]──▶ Check is_active ──▶ Check 2FA required
                                               │
                              [2FA off]──▶ createSession() ──▶ Redirect
                              [2FA on] ──▶ Store pending ──▶ 2FA challenge
                                               │
                                         verifyTOTP()
                                               │
                                         createSession() ──▶ Redirect
```

---

## Komponen

### 1. Controller — `AuthController`

```
AuthController
├── showLogin(): View
├── login(LoginRequest): RedirectResponse
├── logout(Request): RedirectResponse
├── showRegister(): View
├── register(RegisterRequest): RedirectResponse
├── showForgotPassword(): View
├── sendResetLink(ForgotRequest): RedirectResponse
├── showResetForm(token): View
└── resetPassword(ResetRequest): RedirectResponse
```

### 2. Service — `AuthService`

```
AuthService
├── attempt(array $credentials): bool
├── logout(): void
├── register(array $data): User
├── sendPasswordResetLink(string $email): bool
├── resetPassword(array $data): bool
├── lockAccount(User $user): void
├── unlockAccount(User $user): void
└── isAccountLocked(User $user): bool
```

### 3. Service — `TwoFactorService`

```
TwoFactorService
├── enable(User $user): array   // Returns QR code + backup codes
├── disable(User $user): bool
├── verify(User $user, string $code): bool
├── generateBackupCodes(User $user): array
└── useBackupCode(User $user, string $code): bool
```

### 4. Service — `TokenService`

```
TokenService
├── createToken(User $user, string $name, array $abilities): string
├── revokeToken(User $user, int $tokenId): bool
├── revokeAllTokens(User $user): bool
├── listTokens(User $user): Collection
└── validateToken(string $token): ?User
```

### 5. Form Requests

| Request | Validasi |
|---------|----------|
| `LoginRequest` | email (required, email), password (required) |
| `RegisterRequest` | name, email (unique), password (confirmed, min:8) |
| `ForgotPasswordRequest` | email (required, email, exists:users) |
| `ResetPasswordRequest` | token, email, password (confirmed, policy) |
| `ChangePasswordRequest` | current_password (verified), password (confirmed, policy) |
| `TwoFactorVerifyRequest` | code (required, numeric, digits:6) |

### 6. Guards

| Guard | Driver | Digunakan Untuk |
|-------|--------|-----------------|
| `web` | session | Admin Panel, Member Portal |
| `api` | sanctum | REST API Requests |
| `member` | session | Khusus member perpustakaan |

### 7. Middleware

| Middleware | Fungsi |
|------------|--------|
| `auth` | Pastikan user sudah login |
| `auth:api` | Validasi API token |
| `2fa` | Pastikan 2FA sudah diverifikasi |
| `verified` | Pastikan email sudah diverifikasi |
| `throttle:login` | Rate limit login attempts |
| `active.user` | Pastikan akun tidak dinonaktifkan |

---

## Lifecycle

### Registration Lifecycle

```
1. User mengisi form registrasi
2. RegisterRequest validasi input
3. AuthService::register()
   ├── Hash password (bcrypt, cost=12)
   ├── Create User record
   ├── Assign default role
   ├── Send email verification
   └── Log::info('user.registered')
4. Redirect ke halaman verifikasi
```

### Login Lifecycle

```
1. User submit login form / API request
2. Throttle check (max 5 attempts per 1 menit)
3. AuthService::attempt()
   ├── Verify credentials vs DB
   ├── Check is_active flag
   ├── Check account_locked_until
   └── Create session / token
4. 2FA check (jika diaktifkan)
5. Log::info('user.login')
6. Redirect / Return token
```

### Password Reset Lifecycle

```
1. User request reset link
2. Validate email exists
3. Generate secure token (60 chars)
4. Store in password_reset_tokens table
5. Send email dengan link reset
6. User klik link → validasi token (max 60 menit)
7. User input password baru
8. Update password, delete token, revoke all sessions
9. Notify user via email
```

### Token Lifecycle (API)

```
1. POST /api/v1/auth/login → createToken()
2. Token dikirim via Authorization: Bearer {token}
3. Sanctum middleware validasi token
4. Token expires sesuai config atau manual revoke
5. POST /api/v1/auth/logout → revokeToken()
```

---

## Konfigurasi

### Database Tables

**Tabel `users`** (bagian dari User Engine)

**Tabel `password_reset_tokens`**
```sql
CREATE TABLE password_reset_tokens (
    email       VARCHAR(255) PRIMARY KEY,
    token       VARCHAR(255) NOT NULL,
    created_at  TIMESTAMP NULL,
    INDEX idx_prt_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**Tabel `auth_logs`**
```sql
CREATE TABLE auth_logs (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id     BIGINT UNSIGNED NULL,
    event       VARCHAR(50)  NOT NULL,  -- login, logout, failed, locked
    email       VARCHAR(255) NULL,
    ip_address  VARCHAR(45)  NULL,
    user_agent  TEXT         NULL,
    metadata    JSON         NULL,
    created_at  TIMESTAMP    NULL,
    INDEX idx_al_user (user_id),
    INDEX idx_al_event (event),
    INDEX idx_al_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**Tabel `two_factor_codes`**
```sql
CREATE TABLE two_factor_codes (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id         BIGINT UNSIGNED NOT NULL,
    secret          TEXT NOT NULL,
    backup_codes    JSON NULL,
    is_confirmed    TINYINT(1) NOT NULL DEFAULT 0,
    created_at      TIMESTAMP NULL,
    updated_at      TIMESTAMP NULL,
    UNIQUE KEY uq_2fa_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### Konfigurasi `config/auth.php`

```php
return [
    'defaults' => [
        'guard'     => 'web',
        'passwords' => 'users',
    ],
    'guards' => [
        'web' => ['driver' => 'session', 'provider' => 'users'],
        'api' => ['driver' => 'sanctum', 'provider' => 'users'],
    ],
    'providers' => [
        'users' => ['driver' => 'eloquent', 'model' => App\Models\User::class],
    ],
];
```

### Environment Variables

```env
# Auth
SESSION_LIFETIME=120
SESSION_ENCRYPT=true

# Password Policy
AUTH_PASSWORD_MIN_LENGTH=8
AUTH_PASSWORD_REQUIRE_UPPERCASE=true
AUTH_PASSWORD_REQUIRE_NUMBER=true
AUTH_PASSWORD_REQUIRE_SYMBOL=false

# Brute Force
AUTH_MAX_ATTEMPTS=5
AUTH_LOCKOUT_MINUTES=15
AUTH_THROTTLE_DECAY=60

# Token (API)
SANCTUM_STATEFUL_DOMAINS=localhost
API_TOKEN_EXPIRY_DAYS=30

# 2FA
TWO_FACTOR_ENABLED=false
TWO_FACTOR_ISSUER="CosmicLib"
```

---

## Integrasi

### Dengan Permission Engine
```php
// Setelah login, load user permissions
$user->load('roles.permissions');
// Auth Engine memastikan akun valid, Permission Engine menentukan akses
```

### Dengan User Engine
- Auth Engine menggunakan model `User` dari User Engine
- Sinkronisasi `is_active`, `last_login_at`, `login_attempts`

### Dengan Setting Engine
```php
$maxAttempts = setting('security.max_login_attempts', 5);
$lockMinutes = setting('security.lockout_minutes', 15);
$sessionLife = setting('security.session_lifetime', 120);
```

### Dengan Notification Engine
- Kirim email verifikasi saat registrasi
- Kirim email reset password
- Kirim alert login mencurigakan
- Kirim notifikasi akun terkunci

### Dengan Log Engine
- Log semua event autentikasi (login, logout, failed, locked)
- Log perubahan password
- Log aktivasi/nonaktifasi 2FA

---

## AI Rules

```yaml
auth_engine_rules:
  - SELALU hash password dengan bcrypt min cost=12, JANGAN simpan plain text
  - WAJIB gunakan rate limiting pada semua endpoint autentikasi
  - JANGAN expose detail error login (jangan tulis "password salah", tulis "kredensial tidak valid")
  - WAJIB invalidate semua session/token saat password berubah
  - JANGAN simpan token di localStorage — gunakan HttpOnly Cookie atau Authorization header
  - WAJIB log semua event autentikasi (sukses dan gagal)
  - JANGAN buat logika auth di Controller — gunakan AuthService
  - WAJIB validasi email_verified sebelum akses halaman sensitif
  - SELALU gunakan HTTPS untuk endpoint auth
  - JANGAN bypass middleware auth untuk testing — gunakan user factories
```

---

## Best Practice

1. **Password Hashing** — Gunakan `bcrypt` dengan cost factor 12 (atau `argon2id`)
2. **Timing Attack Prevention** — Gunakan `hash_equals()` untuk perbandingan token
3. **Secure Session** — Set `SESSION_ENCRYPT=true`, `SESSION_SECURE_COOKIE=true`
4. **Token Rotation** — Rotasi token setiap session aktif setelah login
5. **Least Privilege** — Default role memiliki permission minimal
6. **Clear Feedback** — Jangan beri informasi berlebih pada error auth
7. **2FA Encouragement** — Tampilkan prompt untuk mengaktifkan 2FA setelah login
8. **Audit Everything** — Semua event auth harus masuk audit log

---

## Checklist

### Implementasi
- [ ] `AuthController` dengan semua method login/logout/register
- [ ] `AuthService` dengan logika business
- [ ] `TwoFactorService` dengan TOTP support
- [ ] `TokenService` untuk API authentication
- [ ] Semua Form Request dengan validasi
- [ ] Guard `web` dan `api` terkonfigurasi
- [ ] Semua middleware auth terdaftar
- [ ] Rate limiting pada endpoint auth
- [ ] Brute force protection + account lockout
- [ ] Password reset via email
- [ ] Email verification flow
- [ ] Auth log table dan events

### Security
- [ ] Password hashing bcrypt cost≥12
- [ ] CSRF protection aktif
- [ ] Session encryption aktif
- [ ] Secure cookie flags
- [ ] HTTPS enforced (middleware)
- [ ] XSS/injection prevention

### Testing
- [ ] Unit test AuthService
- [ ] Feature test login/logout
- [ ] Feature test password reset
- [ ] Feature test 2FA flow
- [ ] Security test brute force
- [ ] Security test token leakage

---

## Roadmap

| Versi | Fitur | Status |
|-------|-------|--------|
| v1.0 | Login/Logout/Register + Session | Planned |
| v1.1 | Password Reset + Email Verification | Planned |
| v1.2 | API Token (Sanctum) | Planned |
| v1.3 | Two-Factor Authentication (TOTP) | Planned |
| v2.0 | OAuth2 (Google, Microsoft) | Future |
| v2.1 | LDAP / Active Directory Integration | Future |
| v2.2 | Biometric / WebAuthn (FIDO2) | Future |

---

## Referensi

- [16_SYSTEM_SETTING_ENGINE.md](16_SYSTEM_SETTING_ENGINE.md) — Setting Engine
- [10_PERMISSION_ENGINE.md](10_PERMISSION_ENGINE.md) — Permission Engine
- [18_USER_ENGINE.md](18_USER_ENGINE.md) — User Engine
- [15_NOTIFICATION_ENGINE.md](15_NOTIFICATION_ENGINE.md) — Notification Engine
- [25_LOG_ENGINE.md](25_LOG_ENGINE.md) — Log Engine
- [42_SECURITY_GUIDE.md](42_SECURITY_GUIDE.md) — Security Guide

---

*Dokumen ini adalah bagian dari CosmicLib Engine Documentation Suite.*
*Dibuat: 2026 | Terakhir diperbarui: 2026-07-19*