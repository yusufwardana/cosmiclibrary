<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

class ThemeManager
{
    private readonly ThemeEngine $engine;

    public function __construct(ThemeEngine $engine)
    {
        $this->engine = $engine;
    }

    public function list(): Collection
    {
        $themesPath = base_path('themes');
        if (! File::isDirectory($themesPath)) {
            return collect();
        }

        return collect(File::directories($themesPath))
            ->mapWithKeys(fn ($dir) => [
                basename($dir) => $this->loadManifest(basename($dir)),
            ])
            ->filter();
    }

    public function loadManifest(string $theme): ?array
    {
        $manifestPath = base_path("themes/{$theme}/theme.json");
        if (! File::exists($manifestPath)) {
            return null;
        }

        return json_decode(File::get($manifestPath), true);
    }

    public function activate(string $theme): bool
    {
        $manifest = $this->loadManifest($theme);
        if (! $manifest) {
            return false;
        }

        $this->engine->setActive($theme);

        return true;
    }

    public function active(): string
    {
        return $this->engine->active();
    }
}
