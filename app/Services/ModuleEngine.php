<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Module;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class ModuleEngine extends BaseService
{
    public function name(): string
    {
        return 'module';
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
     * Discover modules in filesystem and sync to DB.
     */
    public function discover(): void
    {
        $paths = File::directories(base_path('modules'));

        foreach ($paths as $path) {
            $manifest = $path.'/module.json';
            if (! File::exists($manifest)) {
                continue;
            }

            $config = json_decode(File::get($manifest), true);
            if (! is_array($config)) {
                continue;
            }

            $slug = basename($path);
            $enabled = $config['enabled'] ?? true;

            Module::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $config['name'] ?? $slug,
                    'version' => $config['version'] ?? '1.0.0',
                    'description' => $config['description'] ?? null,
                    'provider' => $config['provider'] ?? null,
                    'priority' => $config['priority'] ?? 100,
                    'dependencies' => $config['dependencies'] ?? null,
                    'compatibility' => $config['compatibility'] ?? null,
                    'status' => $enabled ? 'active' : 'installed',
                ]
            );
        }

        Cache::forget('module.engine.all');
    }

    /**
     * Get all modules from DB.
     */
    public function all(): Collection
    {
        return Cache::remember('module.engine.all', 3600, fn () => Module::orderBy('priority')->get());
    }

    /**
     * Get active modules only.
     */
    public function active(): Collection
    {
        return $this->all()->where('status', 'active');
    }

    /**
     * Check if a module exists.
     */
    public function has(string $slug): bool
    {
        return Module::where('slug', $slug)->exists();
    }

    /**
     * Get module manifest.
     */
    public function get(string $slug): ?Module
    {
        return Module::where('slug', $slug)->first();
    }

    /**
     * Install a module from filesystem.
     */
    public function install(string $slug): bool
    {
        $path = base_path("modules/{$slug}/module.json");
        if (! File::exists($path)) {
            Log::warning("[ModuleEngine] Module manifest not found: {$slug}");

            return false;
        }

        $config = json_decode(File::get($path), true);
        if (! is_array($config)) {
            return false;
        }

        Module::updateOrCreate(
            ['slug' => $slug],
            [
                'name' => $config['name'] ?? $slug,
                'version' => $config['version'] ?? '1.0.0',
                'description' => $config['description'] ?? null,
                'provider' => $config['provider'] ?? null,
                'priority' => $config['priority'] ?? 100,
                'dependencies' => $config['dependencies'] ?? null,
                'compatibility' => $config['compatibility'] ?? null,
                'status' => 'installed',
            ]
        );

        Cache::forget('module.engine.all');

        return true;
    }

    /**
     * Enable a module.
     */
    public function enable(string $slug): bool
    {
        $module = Module::where('slug', $slug)->first();
        if (! $module) {
            return false;
        }

        if (! $this->checkDependencies($module)) {
            Log::warning("[ModuleEngine] Unmet dependencies for: {$slug}");

            return false;
        }

        $module->update(['status' => 'active']);
        Cache::forget('module.engine.all');

        return true;
    }

    /**
     * Disable a module (keep in DB).
     */
    public function disable(string $slug): bool
    {
        $affected = Module::where('slug', $slug)->where('status', 'active')->update(['status' => 'installed']);
        Cache::forget('module.engine.all');

        return $affected > 0;
    }

    /**
     * Remove module record from DB (uninstall).
     */
    public function remove(string $slug): bool
    {
        $affected = Module::where('slug', $slug)->delete();
        Cache::forget('module.engine.all');

        return $affected > 0;
    }

    /**
     * Check if module's dependencies are satisfied.
     */
    private function checkDependencies(Module $module): bool
    {
        $deps = $module->dependencies;
        if (empty($deps)) {
            return true;
        }

        $active = $this->active()->keyBy('slug');

        foreach ((array) $deps as $dep) {
            $depSlug = is_string($dep) ? $dep : ($dep['module'] ?? null);
            if (! $depSlug || ! $active->has($depSlug)) {
                return false;
            }
        }

        return true;
    }
}