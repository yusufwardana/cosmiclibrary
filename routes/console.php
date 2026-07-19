<?php

declare(strict_types=1);

use App\Jobs\ExpireReservations;
use App\Jobs\MarkOverdueRecords;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::job(new MarkOverdueRecords)->dailyAt('00:01');
Schedule::job(new ExpireReservations)->hourly();
Schedule::command('backup:create --type=full')->dailyAt('01:00');
Schedule::command('backup:prune --keep=5')->weekly()->sundays()->at('02:00');
Schedule::command('media:gc')->weekly()->sundays()->at('03:00');
