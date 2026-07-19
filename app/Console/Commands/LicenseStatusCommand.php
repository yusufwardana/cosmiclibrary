<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\LicenseEngine;
use Illuminate\Console\Command;

class LicenseStatusCommand extends Command
{
    protected $signature = 'license:status';
    protected $description = 'Show current license status';

    public function handle(LicenseEngine $engine): int
    {
        $license = $engine->current();

        if (! $license) {
            $this->warn('No active license found.');

            return self::SUCCESS;
        }

        $this->table(
            ['Key', 'Edition', 'Status', 'Domain', 'Expires', 'Validated'],
            [[
                $license->license_key,
                $license->edition,
                $license->status,
                $license->domain ?? '-',
                $license->expires_at?->format('Y-m-d') ?? 'Never',
                $license->last_validated_at?->diffForHumans() ?? '-',
            ]]
        );

        return self::SUCCESS;
    }
}