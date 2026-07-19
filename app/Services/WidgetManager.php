<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Collection;

class WidgetManager
{
    private readonly WidgetEngine $engine;

    public function __construct(WidgetEngine $engine)
    {
        $this->engine = $engine;
    }

    public function all(): array
    {
        return $this->engine->all();
    }

    public function location(string $location): Collection
    {
        return $this->engine->location($location);
    }

    public function register(string $location, array $widgets): void
    {
        $this->engine->register($location, $widgets);
    }
}
