<?php

declare(strict_types=1);

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Runtime cache of permission slugs for the current user instance.
     * Avoids N+1 queries when policies check many permissions per request.
     */
    protected ?Collection $permissionCache = null;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function member(): HasOne
    {
        return $this->hasOne(Member::class);
    }

    public function hasPermission(string $slug): bool
    {
        return $this->permissionSlugs()->contains($slug);
    }

    /**
     * Get all permission slugs for this user, cached for the instance lifetime.
     * Eager-loads roles.permissions in a single query to prevent N+1.
     */
    public function permissionSlugs(): Collection
    {
        if ($this->permissionCache instanceof Collection) {
            return $this->permissionCache;
        }

        $roles = $this->roles()->with('permissions')->get();

        // @phpstan-ignore-next-line
        $slugs = $roles->flatMap(fn (Role $role) => $role->permissions->pluck('slug'))
            ->unique()
            ->values();

        return $this->permissionCache = $slugs;
    }

    /**
     * Flush the in-memory permission cache. Call after mutating roles/permissions.
     */
    public function refreshPermissionCache(): void
    {
        $this->permissionCache = null;
    }
}
