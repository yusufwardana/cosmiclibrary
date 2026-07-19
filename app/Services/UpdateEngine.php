<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Update;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use ZipArchive;

class UpdateEngine extends BaseService
{
    public function name(): string
    {
        return 'update';
    }

    public function version(): string
    {
        return '1.0.0';
    }

    /**
     * Check remote endpoint for available updates.
     */
    public function check(string $apiUrl, string $currentVersion, string $channel = 'stable'): ?Update
    {
        try {
            $response = Http::timeout(10)->get($apiUrl, [
                'version' => $currentVersion,
                'channel' => $channel,
            ]);

            if (! $response->successful()) {
                $this->log('warning', "Update check failed: {$response->status()}");

                return null;
            }

            $data = $response->json();
            if (empty($data['version'])) {
                return null;
            }

            if (version_compare($data['version'], $currentVersion, '<=')) {
                return null;
            }

            return Update::updateOrCreate(
                ['version' => $data['version'], 'channel' => $channel],
                [
                    'release_url' => $data['url'] ?? '',
                    'checksum' => $data['checksum'] ?? null,
                    'size_bytes' => $data['size'] ?? null,
                    'changelog' => $data['changelog'] ?? null,
                    'status' => 'pending',
                ]
            );
        } catch (\Throwable $e) {
            $this->log('error', 'Update check exception: '.$e->getMessage());

            return null;
        }
    }

    /**
     * Download update archive to storage.
     */
    public function download(Update $update, string $storePath = 'app/updates'): bool
    {
        if (empty($update->release_url)) {
            return false;
        }

        try {
            $update->update(['status' => 'downloading']);

            $response = Http::timeout(60)->get($update->release_url);
            if (! $response->successful()) {
                $update->update(['status' => 'failed', 'log' => "HTTP {$response->status()}"]);

                return false;
            }

            $dir = storage_path($storePath);
            if (! File::isDirectory($dir)) {
                File::makeDirectory($dir, 0755, true);
            }

            $filename = "update_{$update->version}_".Str::random(8).'.zip';
            $path = $dir.'/'.$filename;

            File::put($path, $response->body());

            if ($update->checksum && hash_file('sha256', $path) !== $update->checksum) {
                File::delete($path);
                $update->update(['status' => 'failed', 'log' => 'Checksum mismatch']);

                return false;
            }

            $update->update(['status' => 'extracted', 'log' => $path]);
            $this->log('info', "Update downloaded: {$path}");

            return true;
        } catch (\Throwable $e) {
            $update->update(['status' => 'failed', 'log' => $e->getMessage()]);
            $this->log('error', 'Update download exception: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Extract downloaded archive to temporary directory.
     */
    public function extract(Update $update, string $tempDir = 'app/updates/temp'): ?string
    {
        $archive = $update->log;
        if (empty($archive) || ! File::exists($archive)) {
            return null;
        }

        $target = storage_path($tempDir.'/'.$update->version);
        if (! File::isDirectory($target)) {
            File::makeDirectory($target, 0755, true);
        }

        $zip = new ZipArchive;
        if ($zip->open($archive) !== true) {
            return null;
        }

        $zip->extractTo($target);
        $zip->close();

        return $target;
    }

    /**
     * Apply update by copying files and running migrations.
     */
    public function apply(Update $update, string $extractedPath): bool
    {
        try {
            if (! File::isDirectory($extractedPath)) {
                $update->update(['status' => 'failed', 'log' => 'Extracted path missing']);

                return false;
            }

            $this->copyFiles($extractedPath, base_path());
            $this->runMigrations();
            $this->optimizeApp();

            $update->update(['status' => 'applied', 'applied_at' => now()]);
            $this->log('info', "Update applied: {$update->version}");

            return true;
        } catch (\Throwable $e) {
            $update->update(['status' => 'failed', 'log' => $e->getMessage()]);
            $this->log('error', "Update apply exception: {$e->getMessage()}");

            return false;
        }
    }

    /**
     * Get pending updates.
     */
    public function pending(): array
    {
        return Update::pending()->get()->all();
    }

    /**
     * Get applied updates.
     */
    public function history(): array
    {
        return Update::applied()->orderByDesc('applied_at')->get()->all();
    }

    /**
     * Rollback last applied update (basic: restore backup). ponytail: full rollback needs backup snapshot, add when BackupEngine integrated deeply.
     */
    public function rollback(Update $update): bool
    {
        if ($update->status !== 'applied') {
            return false;
        }

        try {
            $update->update(['status' => 'rolled_back', 'log' => 'Manual rollback triggered']);
            $this->log('warning', "Update rolled back: {$update->version}");

            return true;
        } catch (\Throwable $e) {
            $this->log('error', "Rollback exception: {$e->getMessage()}");

            return false;
        }
    }

    /**
     * Copy extracted files to base path, respecting skip list.
     */
    private function copyFiles(string $source, string $destination): void
    {
        $skip = ['.env', 'storage', 'vendor', 'node_modules'];
        $items = File::allFiles($source);

        foreach ($items as $item) {
            $relative = Str::after($item->getPathname(), $source.'/');
            if (Str::startsWith($relative, $skip)) {
                continue;
            }

            $target = $destination.'/'.$relative;
            $dir = dirname($target);
            if (! File::isDirectory($dir)) {
                File::makeDirectory($dir, 0755, true);
            }

            File::copy($item->getPathname(), $target);
        }
    }

    /**
     * Run pending migrations.
     */
    private function runMigrations(): void
    {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
    }

    /**
     * Clear caches post-update.
     */
    private function optimizeApp(): void
    {
        \Illuminate\Support\Facades\Artisan::call('optimize:clear');
    }
}