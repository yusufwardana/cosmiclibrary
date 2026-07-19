<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\PluginEngine;
use Illuminate\Console\Command;

class PluginEnableCommand extends Command
{
    protected $signature = 'plugin:enable {slug : Plugin slug}';
    protected $description = 'Enable a plugin';

    public function handle(PluginEngine $plugins): int
    {
        $slug = $this->argument('slug');

        if ($plugins->enable($slug)) {
            $this->info("Plugin [{$slug}] enabled.");

            return self::SUCCESS;
        }

        $this->error("Failed to enable plugin [{$slug}].");

        return self::FAILURE;
    }
}