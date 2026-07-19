<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\ModuleEngine;
use Illuminate\Console\Command;

class ModuleEnableCommand extends Command
{
    protected $signature = 'module:enable {slug : Module slug}';
    protected $description = 'Enable a module';

    public function handle(ModuleEngine $modules): int
    {
        $slug = $this->argument('slug');

        if ($modules->enable($slug)) {
            $this->info("Module [{$slug}] enabled.");

            return self::SUCCESS;
        }

        $this->error("Failed to enable module [{$slug}].");

        return self::FAILURE;
    }
}