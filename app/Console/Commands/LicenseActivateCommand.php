<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\LicenseEngine;
use Illuminate\Console\Command;

class LicenseActivateCommand extends Command
{
    protected $signature = 'license:activate {key} {email} {--endpoint= : Remote endpoint} {--domain= : Domain}';
    protected $description = 'Activate a license key';

    public function handle(LicenseEngine $engine): int
    {
        $endpoint = $this->option('endpoint') ?? config('app.license_url', 'https://api.cosmiclib.dev/license/activate');

        $license = $engine->activate(
            $this->argument('key'),
            $this->argument('email'),
            $endpoint,
            $this->option('domain')
        );

        if (! $license) {
            $this->error('Activation failed.');

            return self::FAILURE;
        }

        $this->info("License activated: {$license->license_key} ({$license->edition})");

        return self::SUCCESS;
    }
}