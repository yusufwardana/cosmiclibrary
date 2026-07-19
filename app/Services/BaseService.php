<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\EngineContract;
use Illuminate\Support\Facades\Log;

abstract class BaseService implements EngineContract
{
    abstract public function name(): string;

    abstract public function version(): string;

    public function boot(): void
    {
        // Override in subclass to register routes, views, etc.
    }

    /**
     * Log service activity with context.
     */
    protected function log(string $level, string $message, array $context = []): void
    {
        Log::channel('stack')->log($level, sprintf('[%s] %s', static::class, $message), $context);
    }
}
