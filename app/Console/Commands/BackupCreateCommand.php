<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\BackupEngine;
use Illuminate\Console\Command;

class BackupCreateCommand extends Command
{
    protected $signature = 'backup:create {--type=full : Backup type (full|db)}';
    protected $description = 'Create a new backup';

    public function handle(BackupEngine $engine): int
    {
        $type = $this->option('type');
        $this->info('Starting backup...');

        $backup = $engine->create($type);

        if ($backup->status === 'completed') {
            $sizeMb = round($backup->size / 1024 / 1024, 2);
            $this->info("Backup created: {$backup->filename} ({$sizeMb} MB)");

            return self::SUCCESS;
        }

        $this->error("Backup failed: {$backup->notes}");

        return self::FAILURE;
    }
}