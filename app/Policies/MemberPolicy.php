<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Member;
use App\Models\User;

class MemberPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('member.view');
    }

    public function view(User $user, Member $member): bool
    {
        return $user->hasPermission('member.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('member.create');
    }

    public function update(User $user, Member $member): bool
    {
        return $user->hasPermission('member.update');
    }

    public function delete(User $user, Member $member): bool
    {
        return $user->hasPermission('member.delete');
    }
}
