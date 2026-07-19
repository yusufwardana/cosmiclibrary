<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\BackupEngine;
use App\Services\InstallerEngine;
use App\Services\MediaEngine;
use App\Services\ModuleEngine;
use App\Services\PermissionEngine;
use App\Services\SettingEngine;
use App\Services\ThemeEngine;
use Illuminate\Database\QueryException;
use Illuminate\Support\ServiceProvider;

class CoreServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ModuleEngine::class, fn () => new ModuleEngine);
        $this->app->singleton(ThemeEngine::class, fn () => new ThemeEngine);
        $this->app->singleton(PermissionEngine::class, fn () => new PermissionEngine);
        $this->app->singleton(SettingEngine::class, fn () => new SettingEngine);
        $this->app->singleton(InstallerEngine::class, fn () => new InstallerEngine);
        $this->app->singleton(MediaEngine::class, fn () => new MediaEngine);
        $this->app->singleton(BackupEngine::class, fn () => new BackupEngine);
    }

    public function boot(): void
    {
        $engines = [
            ModuleEngine::class,
            ThemeEngine::class,
            PermissionEngine::class,
            SettingEngine::class,
            InstallerEngine::class,
        ];

        foreach ($engines as $engine) {
            try {
                $this->app->make($engine)->boot();
            } catch (QueryException $e) {
                // Skip DB-dependent engines when connection unavailable (e.g. during install or tests)
                report($e);
            }
        }
    }
}
