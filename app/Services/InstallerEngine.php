<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

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

        $this->writeEnv($data);

        $this->runCommand('php artisan migrate --force');

        $this->runCommand('php artisan db:seed --class=Database\\Seeders\\CoreSeeder --force');

        $this->createAdmin($data);

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
                '.env.example' => is_writable(base_path('.env.example')),
                'storage/' => is_writable(storage_path()),
                'bootstrap/cache/' => is_writable(base_path('bootstrap/cache')),
            ],
        ];
    }

    private function assertEnvironment(): void
    {
        $minVersion = config('installer.php_version');
        if (version_compare(PHP_VERSION, $minVersion, '<')) {
            throw new \RuntimeException('PHP version too low');
        }

        foreach (config('installer.extensions') as $ext) {
            if (! extension_loaded($ext)) {
                throw new \RuntimeException("Missing PHP extension: {$ext}");
            }
        }
    }

    private function writeEnv(array $data): void
    {
        // ponytail: keep existing .env if present to preserve APP_KEY continuity
        if (! File::exists(base_path('.env'))) {
            $template = base_path('.env.example');
            if (! File::exists($template)) {
                throw new \RuntimeException('.env.example not found');
            }

            File::copy($template, base_path('.env'));
        }

        $content = File::get(base_path('.env'));

        $map = [
            'APP_NAME' => $data['school_name'] ?? 'CosmicLib',
            'APP_KEY' => 'base64:' . base64_encode(Str::random(32)),
            'APP_URL' => $data['app_url'] ?? 'http://localhost',
            'DB_CONNECTION' => 'mysql',
            'DB_HOST' => $data['db_host'] ?? '127.0.0.1',
            'DB_PORT' => $data['db_port'] ?? '3306',
            'DB_DATABASE' => $data['db_database'] ?? 'cosmiclib',
            'DB_USERNAME' => $data['db_username'] ?? 'root',
            'DB_PASSWORD' => $data['db_password'] ?? '',
            'MAIL_MAILER' => $data['mail_driver'] ?? 'log',
            'MAIL_HOST' => $data['mail_host'] ?? '',
            'MAIL_PORT' => $data['mail_port'] ?? '587',
            'MAIL_USERNAME' => $data['mail_username'] ?? '',
            'MAIL_PASSWORD' => $data['mail_password'] ?? '',
            'MAIL_ENCRYPTION' => $data['mail_encryption'] ?? 'tls',
            'MAIL_FROM_ADDRESS' => $data['mail_from_address'] ?? 'noreply@library.test',
            'MAIL_FROM_NAME' => $data['mail_from_name'] ?? 'CosmicLib',
        ];

        foreach ($map as $key => $value) {
            $pattern = '/^' . preg_quote($key, '/') . '=.*$/m';
            $replacement = $key . '=' . $value;
            if (preg_match($pattern, $content)) {
                $content = preg_replace($pattern, $replacement, $content);
            } else {
                $content .= "\n" . $replacement;
            }
        }

        File::put(base_path('.env'), $content);
    }

    private function runCommand(string $command): void
    {
        $process = Process::fromShellCommandline($command);
        $process->setTimeout(300);
        $process->run(function ($type, $buffer) {
            $this->log('info', $buffer);
        });

        if (! $process->isSuccessful()) {
            $error = $process->getErrorOutput();
            $output = $process->getOutput();
            $full = trim($error . "\n" . $output);
            throw new \RuntimeException("Command failed: {$command}\nOutput: {$full}");
        }
    }

    private function createAdmin(array $data): void
    {
        $this->runCommand(sprintf(
            'php artisan db:seed --class=Database\\Seeders\\AdminSeeder --force -- --name=%s --email=%s --password=%s',
            escapeshellarg($data['admin_name'] ?? 'Admin'),
            escapeshellarg($data['admin_email'] ?? 'admin@library.test'),
            escapeshellarg($data['admin_password'] ?? 'password')
        ));
    }

    private function createLockFile(): void
    {
        $path = config('installer.lock_file');
        File::put($path, 'installed');
    }
}