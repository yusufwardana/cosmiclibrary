<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Reservation;
use App\Models\User;

class ReservationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('reservation.view');
    }

    public function view(User $user, Reservation $reservation): bool
    {
        return $user->hasPermission('reservation.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('reservation.create');
    }

    public function cancel(User $user, Reservation $reservation): bool
    {
        return $user->hasPermission('reservation.cancel');
    }
}
