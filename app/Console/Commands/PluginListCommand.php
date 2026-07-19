<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\PluginEngine;
use Illuminate\Console\Command;

class PluginListCommand extends Command
{
    protected $signature = 'plugin:list';
    protected $description = 'List all registered plugins';

    public function handle(PluginEngine $plugins): int
    {
        $all = $plugins->all();

        if ($all->isEmpty()) {
            $this->warn('No plugins found.');

            return self::SUCCESS;
        }

        $rows = $all->map(fn ($p) => [$p->slug, $p->name, $p->version, $p->hook ?? '-', $p->is_active ? 'active' : 'inactive'])->all();
        $this->table(['Slug', 'Name', 'Version', 'Hook', 'Status'], $rows);

        return self::SUCCESS;
    }
}