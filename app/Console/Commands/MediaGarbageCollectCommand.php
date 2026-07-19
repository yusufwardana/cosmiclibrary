<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\MediaEngine;
use Illuminate\Console\Command;

class MediaGarbageCollectCommand extends Command
{
    protected $signature = 'media:gc';
    protected $description = 'Delete orphaned media files (no model reference)';

    public function handle(MediaEngine $engine): int
    {
        $count = $engine->garbageCollect();
        $this->info("Deleted {$count} orphaned media file(s).");

        return self::SUCCESS;
    }
}