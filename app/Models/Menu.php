<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Menu extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function items(): HasMany
    {
        return $this->hasMany(MenuItem::class)->orderBy('sort_order');
    }

    /**
     * Get tree of active items filtered by permissions.
     */
    public function tree(?Collection $permissions = null): Collection
    {
        $items = $this->items()
            ->where('is_active', true)
            ->get()
            ->filter(function ($item) use ($permissions) {
                if (! $item->permission) {
                    return true;
                }

                return $permissions?->contains('slug', $item->permission) ?? false;
            });

        return $this->buildTree($items);
    }

    private function buildTree(Collection $items, ?int $parentId = null): Collection
    {
        return $items->where('parent_id', $parentId)->values()->map(function ($item) use ($items) {
            $item->children = $this->buildTree($items, $item->id);

            return $item;
        });
    }
}
