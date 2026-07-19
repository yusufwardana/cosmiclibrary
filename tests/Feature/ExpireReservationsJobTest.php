<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Jobs\ExpireReservations;
use App\Models\Reservation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpireReservationsJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_expires_pending_reservations_past_deadline(): void
    {
        // Observer sets expires_at on created; override after creation
        $expired = Reservation::factory()->create(['status' => 'pending']);
        $expired->update(['expires_at' => now()->subDay()]);

        $active = Reservation::factory()->create(['status' => 'pending']);

        (new ExpireReservations)->handle();

        $expired->refresh();
        $active->refresh();

        $this->assertSame('expired', $expired->status);
        $this->assertSame('pending', $active->status);
    }

    public function test_does_not_touch_non_pending_reservations(): void
    {
        $fulfilled = Reservation::factory()->create([
            'expires_at' => now()->subDay(),
            'status' => 'fulfilled',
        ]);

        (new ExpireReservations)->handle();

        $fulfilled->refresh();

        $this->assertSame('fulfilled', $fulfilled->status);
    }
}
