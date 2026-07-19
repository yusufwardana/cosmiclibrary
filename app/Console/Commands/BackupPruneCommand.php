<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\BackupEngine;
use Illuminate\Console\Command;

class BackupPruneCommand extends Command
{
    protected $signature = 'backup:prune {--keep=5 : Number of backups to keep}';
    protected $description = 'Delete old backups keeping only N latest';

    public function handle(BackupEngine $engine): int
    {
        $keep = (int) $this->option('keep');
        $count = $engine->prune($keep);
        $this->info("Pruned {$count} backup(s), kept {$keep}.");

        return self::SUCCESS;
    }
}