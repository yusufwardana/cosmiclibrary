<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\ModuleEngine;
use Illuminate\Console\Command;

class ModuleListCommand extends Command
{
    protected $signature = 'module:list';
    protected $description = 'List all registered modules';

    public function handle(ModuleEngine $modules): int
    {
        $all = $modules->all();

        if ($all->isEmpty()) {
            $this->warn('No modules found.');

            return self::SUCCESS;
        }

        $rows = $all->map(fn ($m) => [$m->slug, $m->name, $m->version, $m->status, $m->priority])->all();
        $this->table(['Slug', 'Name', 'Version', 'Status', 'Priority'], $rows);

        return self::SUCCESS;
    }
}