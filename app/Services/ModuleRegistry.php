<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\File;

class ModuleRegistry
{
    private readonly ModuleEngine $engine;

    public function __construct(ModuleEngine $engine)
    {
        $this->engine = $engine;
    }

    public function all(): array
    {
        return $this->engine->all()->toArray();
    }

    public function exists(string $name): bool
    {
        return $this->engine->has($name);
    }

    public function enable(string $name): bool
    {
        return $this->toggleManifest($name, true);
    }

    public function disable(string $name): bool
    {
        return $this->toggleManifest($name, false);
    }

    private function toggleManifest(string $name, bool $enabled): bool
    {
        $manifestPath = base_path("modules/{$name}/module.json");
        if (! File::exists($manifestPath)) {
            return false;
        }

        $manifest = json_decode(File::get($manifestPath), true);
        $manifest['enabled'] = $enabled;
        File::put($manifestPath, json_encode($manifest, JSON_PRETTY_PRINT));

        $this->engine->boot();

        return true;
    }
}
