<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Reservation;

class ReservationObserver
{
    public function created(Reservation $reservation): void
    {
        // Auto-expire reservation after hold period
        $reservation->update([
            'expires_at' => now()->addHours(48),
        ]);
    }
}
