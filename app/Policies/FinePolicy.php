<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Fine;
use App\Models\User;

class FinePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('fine.view');
    }

    public function view(User $user, Fine $fine): bool
    {
        return $user->hasPermission('fine.view');
    }

    public function pay(User $user, Fine $fine): bool
    {
        return $user->hasPermission('fine.pay');
    }

    public function waive(User $user, Fine $fine): bool
    {
        return $user->hasPermission('fine.waive');
    }
}
