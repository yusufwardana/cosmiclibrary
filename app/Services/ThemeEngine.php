<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Theme;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class ThemeEngine extends BaseService
{
    private ?Theme $activeTheme = null;

    public function name(): string
    {
        return 'theme';
    }

    public function version(): string
    {
        return '1.0.0';
    }

    public function boot(): void
    {
        if (! Schema::hasTable('themes')) {
            return;
        }
        $this->activeTheme = $this->getActiveThemeModel();
        if ($this->activeTheme) {
            $this->registerThemeAssets($this->activeTheme);
        }
    }

    /**
     * Get all registered themes from database.
     */
    public function all(): Collection
    {
        return Cache::remember('theme.engine.all', 3600, fn () => Theme::all());
    }

    /**
     * Get the active theme.
     */
    public function active(): ?Theme
    {
        if (! $this->activeTheme) {
            $this->activeTheme = $this->getActiveThemeModel();
        }

        return $this->activeTheme;
    }

    /**
     * Activate a theme by slug.
     */
    public function setActive(string $slug): void
    {
        Theme::where('is_active', true)->update(['is_active' => false]);
        $theme = Theme::where('slug', $slug)->firstOrFail();
        $theme->update(['is_active' => true]);
        $this->activeTheme = $theme;
        Cache::forget('theme.engine.all');
        Cache::forget('theme.engine.active');
        $this->registerThemeAssets($theme);
    }

    /**
     * Get CSS variable string.
     */
    public function cssVariables(): string
    {
        $theme = $this->active();
        if (! $theme || empty($theme->colors)) {
            return '';
        }

        $vars = [];
        foreach ($theme->colors as $key => $value) {
            $vars[] = "--{$key}: {$value};";
        }

        return implode("\n", $vars);
    }

    /**
     * Get fonts config.
     */
    public function fonts(): array
    {
        $theme = $this->active();

        return $theme?->fonts ?? [];
    }

    /**
     * Get the logo URL for current theme.
     */
    public function logo(): string
    {
        $theme = $this->active();
        if (! $theme) {
            return asset('images/logo-default.png');
        }

        $logoPath = "themes/{$theme->slug}/logo.png";
        $fullPath = public_path($logoPath);

        return File::exists($fullPath) ? asset($logoPath) : asset('images/logo-default.png');
    }

    /**
     * Get the favicon URL for current theme.
     */
    public function favicon(): string
    {
        $theme = $this->active();
        if (! $theme) {
            return asset('favicon.ico');
        }

        $faviconPath = "themes/{$theme->slug}/favicon.ico";
        $fullPath = public_path($faviconPath);

        return File::exists($fullPath) ? asset($faviconPath) : asset('favicon.ico');
    }

    /**
     * Synchronize theme directories with database.
     */
    public function syncFromDisk(): void
    {
        $paths = File::directories(base_path('themes'));
        $existingSlugs = Theme::pluck('slug')->toArray();

        foreach ($paths as $path) {
            $manifest = $path.'/theme.json';
            if (! File::exists($manifest)) {
                continue;
            }
            $config = json_decode(File::get($manifest), true);
            $slug = basename($path);

            Theme::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $config['name'] ?? $slug,
                    'description' => $config['description'] ?? null,
                    'version' => $config['version'] ?? '1.0.0',
                    'author' => $config['author'] ?? null,
                    'path' => $path,
                    'screenshot' => $config['screenshot'] ?? null,
                    'colors' => $config['colors'] ?? null,
                    'fonts' => $config['fonts'] ?? null,
                ]
            );
        }

        Cache::forget('theme.engine.all');
    }

    private function getActiveThemeModel(): ?Theme
    {
        return Cache::remember('theme.engine.active', 3600, fn () => Theme::where('is_active', true)->first());
    }

    private function registerThemeAssets(Theme $theme): void
    {
        // Colors → config so views can access theme.cssVariables()
        config(['theme.active_slug' => $theme->slug]);
        config(['theme.active_colors' => $theme->colors]);
        config(['theme.active_fonts' => $theme->fonts]);
    }
}
