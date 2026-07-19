<?php

declare(strict_types=1);

namespace App\Traits;

trait PluginHook
{
    /**
     * Register plugin hooks.
     * Override in your plugin to return [hookName => callable].
     */
    public function hooks(): array
    {
        return [];
    }

    /**
     * Execute all plugins registered for a given hook.
     */
    public static function runHook(string $hook, mixed $payload = null, ?array $pluginList = null): mixed
    {
        $result = $payload;
        $plugins = $pluginList ?? app(\App\Services\PluginEngine::class)->active()->filter(
            fn ($p) => $p->hook === $hook
        );

        foreach ($plugins as $plugin) {
            $handler = $plugin->handler ?? null;
            if ($handler && is_callable($handler)) {
                $result = $handler($result);
            }
        }

        return $result;
    }
}