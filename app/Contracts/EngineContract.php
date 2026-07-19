<?php

declare(strict_types=1);

namespace App\Contracts;

interface EngineContract
{
    /**
     * Get the engine name identifier.
     */
    public function name(): string;

    /**
     * Boot the engine — called once during service provider registration.
     */
    public function boot(): void;

    /**
     * Return engine version following semver.
     */
    public function version(): string;
}
