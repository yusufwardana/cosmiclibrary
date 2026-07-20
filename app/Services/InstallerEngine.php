<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class InstallerEngine extends BaseService
{
    public function name(): string
    {
        return 'installer';
    }

    public function version(): string
    {
        return '1.0.0';
    }

    /**
     * Run the full installation from wizard session data.
     */
    public function install(array $data): void
    {
        $this->assertEnvironment();
        $this->assertWizardData($data);

        $this->writeEnv($data);
        $this->refreshDatabaseConfig($data);

        Artisan::call('migrate', ['--force' => true]);
        Artisan::call('db:seed', [
            '--class' => 'Database\\Seeders\\CoreSeeder',
            '--force' => true,
        ]);

        $this->createAdmin($data);
        $this->applySchoolSettings($data);
        $this->createLockFile();
    }

    public function isInstalled(): bool
    {
        return File::exists(config('installer.lock_file'));
    }

    public function getRequirements(): array
    {
        return [
            'php' => version_compare(PHP_VERSION, config('installer.php_version', '8.2.0'), '>='),
            'extensions' => collect(config('installer.extensions', []))
                ->mapWithKeys(fn ($ext) => [$ext => extension_loaded($ext)])
                ->all(),
            'writable' => [
                '.env' => ! File::exists(base_path('.env')) || is_writable(base_path('.env')),
                'storage/' => is_writable(storage_path()),
                'bootstrap/cache/' => is_writable(base_path('bootstrap/cache')),
            ],
        ];
    }

    private function assertEnvironment(): void
    {
        $minVersion = config('installer.php_version');
        if (version_compare(PHP_VERSION, $minVersion, '<')) {
            throw new \RuntimeException('Versi PHP terlalu rendah. Minimal '.$minVersion);
        }

        foreach (config('installer.extensions') as $ext) {
            if (! extension_loaded($ext)) {
                throw new \RuntimeException("Ekstensi PHP tidak tersedia: {$ext}");
            }
        }
    }

    private function assertWizardData(array $data): void
    {
        $required = [
            'db_host',
            'db_database',
            'db_username',
            'admin_name',
            'admin_email',
            'admin_password',
            'school_name',
        ];

        foreach ($required as $key) {
            if (! filled($data[$key] ?? null)) {
                throw new \RuntimeException(
                    'Data instalasi tidak lengkap. Silakan ulangi wizard dari langkah Database/Admin.'
                );
            }
        }
    }

    private function writeEnv(array $data): void
    {
        if (! File::exists(base_path('.env'))) {
            $template = base_path('.env.example');
            if (! File::exists($template)) {
                throw new \RuntimeException('.env.example tidak ditemukan');
            }

            File::copy($template, base_path('.env'));
        }

        $content = File::get(base_path('.env'));

        // Keep existing APP_KEY so the current session (and flash errors) stay valid.
        if (! preg_match('/^APP_KEY=.+$/m', $content) || preg_match('/^APP_KEY=\s*$/m', $content)) {
            $this->setEnvValue($content, 'APP_KEY', 'base64:'.base64_encode(Str::random(32)));
        }

        $map = [
            'APP_NAME' => $data['school_name'] ?? 'CosmicLib',
            'APP_URL' => $data['app_url'] ?? config('app.url', 'http://localhost'),
            'DB_CONNECTION' => 'mysql',
            'DB_HOST' => (string) ($data['db_host'] ?? '127.0.0.1'),
            'DB_PORT' => (string) ($data['db_port'] ?? '3306'),
            'DB_DATABASE' => (string) ($data['db_database'] ?? 'cosmiclib'),
            'DB_USERNAME' => (string) ($data['db_username'] ?? 'root'),
            'DB_PASSWORD' => (string) ($data['db_password'] ?? ''),
            'MAIL_MAILER' => (string) ($data['mail_driver'] ?? 'log'),
            'MAIL_HOST' => (string) ($data['mail_host'] ?? ''),
            'MAIL_PORT' => (string) ($data['mail_port'] ?? '587'),
            'MAIL_USERNAME' => (string) ($data['mail_username'] ?? ''),
            'MAIL_PASSWORD' => (string) ($data['mail_password'] ?? ''),
            'MAIL_ENCRYPTION' => (string) ($data['mail_encryption'] ?? 'tls'),
            'MAIL_FROM_ADDRESS' => (string) ($data['mail_from_address'] ?? 'noreply@library.test'),
            'MAIL_FROM_NAME' => (string) ($data['mail_from_name'] ?? 'CosmicLib'),
        ];

        foreach ($map as $key => $value) {
            $this->setEnvValue($content, $key, $value);
        }

        File::put(base_path('.env'), $content);
    }

    private function setEnvValue(string &$content, string $key, string $value): void
    {
        $formatted = $this->formatEnvValue($value);
        $pattern = '/^'.preg_quote($key, '/').'=.*$/m';
        $replacement = $key.'='.$formatted;

        if (preg_match($pattern, $content)) {
            $content = preg_replace($pattern, $replacement, $content) ?? $content;
        } else {
            $content = rtrim($content)."\n".$replacement."\n";
        }
    }

    private function formatEnvValue(string $value): string
    {
        if ($value === '' || preg_match('/[\s#"\'\\\\]/', $value) === 1) {
            return '"'.addcslashes($value, '"\\').'"';
        }

        return $value;
    }

    private function refreshDatabaseConfig(array $data): void
    {
        config([
            'database.default' => 'mysql',
            'database.connections.mysql.host' => $data['db_host'] ?? '127.0.0.1',
            'database.connections.mysql.port' => (string) ($data['db_port'] ?? '3306'),
            'database.connections.mysql.database' => $data['db_database'] ?? 'cosmiclib',
            'database.connections.mysql.username' => $data['db_username'] ?? 'root',
            'database.connections.mysql.password' => $data['db_password'] ?? '',
        ]);

        DB::purge('mysql');
        DB::reconnect('mysql');
    }

    private function createAdmin(array $data): void
    {
        $admin = User::updateOrCreate(
            ['email' => $data['admin_email']],
            [
                'name' => $data['admin_name'],
                'password' => $data['admin_password'],
            ]
        );

        $adminRole = Role::where('slug', 'admin')->first();
        if ($adminRole && ! $admin->roles()->where('role_id', $adminRole->id)->exists()) {
            $admin->roles()->attach($adminRole);
        }
    }

    private function applySchoolSettings(array $data): void
    {
        $settings = [
            ['group' => 'app', 'key' => 'app_name', 'value' => $data['school_name'] ?? 'CosmicLib', 'type' => 'string', 'is_public' => true],
            ['group' => 'school', 'key' => 'school_name', 'value' => $data['school_name'] ?? '', 'type' => 'string', 'is_public' => true],
            ['group' => 'school', 'key' => 'school_address', 'value' => $data['school_address'] ?? '', 'type' => 'string', 'is_public' => true],
            ['group' => 'school', 'key' => 'school_phone', 'value' => $data['school_phone'] ?? '', 'type' => 'string', 'is_public' => true],
            ['group' => 'school', 'key' => 'school_email', 'value' => $data['school_email'] ?? '', 'type' => 'string', 'is_public' => true],
            ['group' => 'school', 'key' => 'school_logo', 'value' => $data['school_logo'] ?? '', 'type' => 'string', 'is_public' => true],
            ['group' => 'app', 'key' => 'app.installed', 'value' => '1', 'type' => 'boolean', 'is_public' => false],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['group' => $setting['group'], 'key' => $setting['key']],
                $setting
            );
        }
    }

    private function createLockFile(): void
    {
        $path = config('installer.lock_file');
        File::put($path, now()->toIso8601String());
    }
}
