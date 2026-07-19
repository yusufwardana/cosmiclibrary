<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\BackupEngine;
use Illuminate\Console\Command;

class BackupRestoreCommand extends Command
{
    protected $signature = 'backup:restore {path : Path to backup ZIP file}';
    protected $description = 'Restore database from a backup file';

    public function handle(BackupEngine $engine): int
    {
        $path = $this->argument('path');

        if (! file_exists($path)) {
            $this->error('Backup file not found.');

            return self::FAILURE;
        }

        $this->warn('This will overwrite current data. Continue?');

        if (! $this->confirm('Proceed with restore?', false)) {
            $this->info('Cancelled.');

            return self::SUCCESS;
        }

        try {
            $engine->restore($path);
            $this->info('Restore completed successfully.');
        } catch (\Throwable $e) {
            $this->error('Restore failed: '.$e->getMessage());

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}