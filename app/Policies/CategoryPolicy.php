<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Category;
use App\Models\User;

class CategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('book.view');
    }

    public function view(User $user, Category $category): bool
    {
        return $user->hasPermission('book.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('book.create');
    }

    public function update(User $user, Category $category): bool
    {
        return $user->hasPermission('book.update');
    }

    public function delete(User $user, Category $category): bool
    {
        return $user->hasPermission('book.delete');
    }
}
