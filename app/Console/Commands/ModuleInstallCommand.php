<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\ModuleEngine;
use Illuminate\Console\Command;

class ModuleInstallCommand extends Command
{
    protected $signature = 'module:install {slug : Module slug/directory name}';
    protected $description = 'Install a module from the filesystem into the database';

    public function handle(ModuleEngine $modules): int
    {
        $slug = $this->argument('slug');

        if ($modules->install($slug)) {
            $this->info("Module [{$slug}] installed successfully.");

            return self::SUCCESS;
        }

        $this->error("Failed to install module [{$slug}].");

            return self::FAILURE;
    }
}