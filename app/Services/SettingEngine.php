<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class SettingEngine extends BaseService
{
    private const CACHE_KEY = 'cosmiclib.settings';

    private const CACHE_TTL = 3600; // 1 hour

    public function name(): string
    {
        return 'setting';
    }

    public function version(): string
    {
        return '1.0.0';
    }

    /**
     * Get a setting value by key.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $settings = $this->cached();
        $setting = $settings->get($key);

        return $setting ? $setting->typedValue() : $default;
    }

    /**
     * Set a setting value.
     */
    public function set(string $key, mixed $value, string $type = 'string', string $group = 'general'): void
    {
        try {
            $storeValue = is_array($value) ? json_encode($value) : (string) $value;

            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $storeValue, 'type' => $type, 'group' => $group]
            );

            $this->clearCache();
        } catch (\Throwable $e) {
            $this->log('error', 'Failed to set setting', ['key' => $key, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Get all settings in a group.
     */
    public function group(string $group): Collection
    {
        return $this->cached()->filter(fn (Setting $s) => $s->group === $group);
    }

    /**
     * Get all settings grouped by group.
     */
    public function all(): Collection
    {
        return $this->cached()->groupBy('group');
    }

    /**
     * Get the type of a setting by key.
     */
    public function getType(string $key): ?string
    {
        $setting = $this->cached()->get($key);

        return $setting?->type;
    }

    /**
     * Check if the application has been installed.
     */
    public function isInstalled(): bool
    {
        try {
            return (bool) $this->get('app.installed', false);
        } catch (\Throwable) {
            return false;
        }
    }

    /**
     * Get all cached settings keyed by setting key.
     */
    private function cached(): Collection
    {
        try {
            return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
                return Setting::all()->keyBy('key');
            });
        } catch (\Throwable) {
            // DB not yet migrated — return empty
            return collect();
        }
    }

    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
