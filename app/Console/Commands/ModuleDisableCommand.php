<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\ModuleEngine;
use Illuminate\Console\Command;

class ModuleDisableCommand extends Command
{
    protected $signature = 'module:disable {slug : Module slug}';
    protected $description = 'Disable a module (keep in database)';

    public function handle(ModuleEngine $modules): int
    {
        $slug = $this->argument('slug');

        if ($modules->disable($slug)) {
            $this->info("Module [{$slug}] disabled.");

            return self::SUCCESS;
        }

        $this->error("Failed to disable module [{$slug}].");

        return self::FAILURE;
    }
}