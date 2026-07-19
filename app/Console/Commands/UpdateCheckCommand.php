<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\UpdateEngine;
use Illuminate\Console\Command;

class UpdateCheckCommand extends Command
{
    protected $signature = 'update:check {--url= : API endpoint} {--version= : Current version} {--channel=stable : Channel}';
    protected $description = 'Check for available updates';

    public function handle(UpdateEngine $engine): int
    {
        $url = $this->option('url') ?? config('app.update_url', 'https://api.cosmiclib.dev/updates');
        $version = $this->option('version') ?? config('app.version', '1.0.0');

        $update = $engine->check($url, $version, $this->option('channel'));

        if (! $update) {
            $this->info('No updates available.');

            return self::SUCCESS;
        }

        $this->info("Update available: {$update->version}");
        $this->line($update->changelog ?? 'No changelog.');

        return self::SUCCESS;
    }
}