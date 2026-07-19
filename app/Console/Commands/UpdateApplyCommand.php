<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Update;
use App\Services\UpdateEngine;
use Illuminate\Console\Command;

class UpdateApplyCommand extends Command
{
    protected $signature = 'update:apply {id : Update ID}';
    protected $description = 'Download, extract and apply an update';

    public function handle(UpdateEngine $engine): int
    {
        $update = Update::find($this->argument('id'));
        if (! $update) {
            $this->error('Update not found.');

            return self::FAILURE;
        }

        if (! $engine->download($update)) {
            $this->error('Download failed.');

            return self::FAILURE;
        }

        $extracted = $engine->extract($update);
        if (! $extracted) {
            $this->error('Extraction failed.');

            return self::FAILURE;
        }

        if ($engine->apply($update, $extracted)) {
            $this->info("Applied update {$update->version}");

            return self::SUCCESS;
        }

        $this->error('Apply failed.');

        return self::FAILURE;
    }
}