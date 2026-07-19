<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ExpireReservations implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        Reservation::where('status', 'pending')
            ->where('expires_at', '<', now())
            ->chunkById(100, function ($reservations): void {
                $reservations->each(fn (Reservation $r) => $r->update(['status' => 'expired']));
            });
    }
}
