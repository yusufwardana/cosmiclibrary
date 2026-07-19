<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\BorrowRecord;
use App\Models\User;

class BorrowRecordPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('borrow.view');
    }

    public function view(User $user, BorrowRecord $borrowRecord): bool
    {
        return $user->hasPermission('borrow.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('borrow.create');
    }

    public function return(User $user, BorrowRecord $borrowRecord): bool
    {
        return $user->hasPermission('borrow.return');
    }

    public function extend(User $user, BorrowRecord $borrowRecord): bool
    {
        return $user->hasPermission('borrow.extend');
    }
}
