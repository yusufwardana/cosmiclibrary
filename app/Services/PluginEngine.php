<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Plugin;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class PluginEngine extends BaseService
{
    public function name(): string
    {
        return 'plugin';
    }

    public function version(): string
    {
        return '1.0.0';
    }

    public function boot(): void
    {
        $this->discover();
    }

    /**
     * Discover plugins in filesystem and sync to DB.
     */
    public function discover(): void
    {
        $paths = File::directories(base_path('plugins'));

        foreach ($paths as $path) {
            $manifest = $path.'/plugin.json';
            if (! File::exists($manifest)) {
                continue;
            }

            $config = json_decode(File::get($manifest), true);
            if (! is_array($config)) {
                continue;
            }

            $slug = basename($path);
            $enabled = $config['enabled'] ?? false;

            Plugin::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $config['name'] ?? $slug,
                    'version' => $config['version'] ?? '1.0.0',
                    'description' => $config['description'] ?? null,
                    'author' => $config['author'] ?? null,
                    'hook' => $config['hook'] ?? null,
                    'settings' => $config['settings'] ?? null,
                    'priority' => $config['priority'] ?? 100,
                    'is_active' => $enabled,
                ]
            );
        }

        Cache::forget('plugin.engine.all');
    }

    /**
     * Get all plugins from DB.
     */
    public function all(): Collection
    {
        return Cache::remember('plugin.engine.all', 3600, fn () => Plugin::orderBy('priority')->get());
    }

    /**
     * Get active plugins only.
     */
    public function active(): Collection
    {
        return $this->all()->where('is_active', true);
    }

    /**
     * Check if a plugin exists.
     */
    public function has(string $slug): bool
    {
        return Plugin::where('slug', $slug)->exists();
    }

    /**
     * Get plugin record.
     */
    public function get(string $slug): ?Plugin
    {
        return Plugin::where('slug', $slug)->first();
    }

    /**
     * Install a plugin from filesystem.
     */
    public function install(string $slug): bool
    {
        $path = base_path("plugins/{$slug}/plugin.json");
        if (! File::exists($path)) {
            Log::warning("[PluginEngine] Plugin manifest not found: {$slug}");

            return false;
        }

        $config = json_decode(File::get($path), true);
        if (! is_array($config)) {
            return false;
        }

        Plugin::updateOrCreate(
            ['slug' => $slug],
            [
                'name' => $config['name'] ?? $slug,
                'version' => $config['version'] ?? '1.0.0',
                'description' => $config['description'] ?? null,
                'author' => $config['author'] ?? null,
                'hook' => $config['hook'] ?? null,
                'settings' => $config['settings'] ?? null,
                'priority' => $config['priority'] ?? 100,
                'is_active' => false,
            ]
        );

        Cache::forget('plugin.engine.all');

        return true;
    }

    /**
     * Enable a plugin.
     */
    public function enable(string $slug): bool
    {
        $plugin = Plugin::where('slug', $slug)->first();
        if (! $plugin) {
            return false;
        }

        $plugin->update(['is_active' => true]);
        Cache::forget('plugin.engine.all');

        return true;
    }

    /**
     * Disable a plugin.
     */
    public function disable(string $slug): bool
    {
        $affected = Plugin::where('slug', $slug)->where('is_active', true)->update(['is_active' => false]);
        Cache::forget('plugin.engine.all');

        return $affected > 0;
    }

    /**
     * Remove plugin record from DB (uninstall).
     */
    public function remove(string $slug): bool
    {
        $affected = Plugin::where('slug', $slug)->delete();
        Cache::forget('plugin.engine.all');

        return $affected > 0;
    }
}