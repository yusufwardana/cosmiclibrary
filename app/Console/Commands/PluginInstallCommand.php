<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\PluginEngine;
use Illuminate\Console\Command;

class PluginInstallCommand extends Command
{
    protected $signature = 'plugin:install {slug : Plugin slug/directory name}';
    protected $description = 'Install a plugin from the filesystem into the database';

    public function handle(PluginEngine $plugins): int
    {
        $slug = $this->argument('slug');

        if ($plugins->install($slug)) {
            $this->info("Plugin [{$slug}] installed successfully.");

            return self::SUCCESS;
        }

        $this->error("Failed to install plugin [{$slug}].");

        return self::FAILURE;
    }
}