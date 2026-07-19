<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\PluginEngine;
use Illuminate\Console\Command;

class PluginDisableCommand extends Command
{
    protected $signature = 'plugin:disable {slug : Plugin slug}';
    protected $description = 'Disable a plugin';

    public function handle(PluginEngine $plugins): int
    {
        $slug = $this->argument('slug');

        if ($plugins->disable($slug)) {
            $this->info("Plugin [{$slug}] disabled.");

            return self::SUCCESS;
        }

        $this->error("Failed to disable plugin [{$slug}].");

        return self::FAILURE;
    }
}