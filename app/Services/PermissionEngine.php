<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Permission;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class PermissionEngine extends BaseService
{
    private const CACHE_KEY = 'cosmiclib.permissions';

    private const CACHE_TTL = 3600; // 1 hour

    private Collection $permissions;

    public function __construct()
    {
        $this->permissions = collect();
    }

    public function name(): string
    {
        return 'permission';
    }

    public function version(): string
    {
        return '1.0.0';
    }

    /**
     * Register permissions from a module into DB if not exists.
     */
    public function register(string $module, array $permissions): void
    {
        foreach ($permissions as $key => $label) {
            $slug = "{$module}.{$key}";
            $this->permissions->put($slug, $label);

            if (Permission::where('slug', $slug)->exists()) {
                continue;
            }

            Permission::create([
                'slug' => $slug,
                'name' => $label,
                'module' => $module,
            ]);
        }

        $this->clearCache();
    }

    /**
     * Get all registered permissions (in-memory).
     */
    public function all(): Collection
    {
        return $this->permissions;
    }

    /**
     * Get all permissions from DB (cached).
     */
    public function allFromDb(): Collection
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, fn () => Permission::all());
    }

    /**
     * Check if a permission exists (in-memory first, then cached DB lookup).
     */
    public function has(string $permission): bool
    {
        if ($this->permissions->has($permission)) {
            return true;
        }

        return $this->allFromDb()->contains(fn (Permission $p) => $p->slug === $permission);
    }

    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
