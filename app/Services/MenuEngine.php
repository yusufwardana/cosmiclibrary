<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Menu;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class MenuEngine extends BaseService
{
    public function name(): string
    {
        return 'menu';
    }

    public function version(): string
    {
        return '1.0.0';
    }

    /**
     * Get all menus.
     */
    public function all(): Collection
    {
        return Cache::remember('menu.engine.all', 3600, fn () => Menu::with('items')->get());
    }

    /**
     * Get a menu by slug.
     */
    public function get(string $slug): ?Menu
    {
        return Cache::remember("menu.engine.{$slug}", 3600, fn () => Menu::where('slug', $slug)->first());
    }

    /**
     * Get tree of a menu by slug, filtered by user permissions.
     */
    public function tree(string $slug, ?Collection $permissions = null): Collection
    {
        $menu = $this->get($slug);
        if (! $menu) {
            return collect();
        }

        return $menu->tree($permissions);
    }

    /**
     * Create or update a menu from definition array.
     */
    public function define(string $slug, string $name, ?string $description = null): Menu
    {
        $menu = Menu::updateOrCreate(
            ['slug' => $slug],
            ['name' => $name, 'description' => $description]
        );
        Cache::forget('menu.engine.all');
        Cache::forget("menu.engine.{$slug}");

        return $menu;
    }

    /**
     * Sync menu items from a definition array.
     */
    public function syncItems(string $slug, array $items): void
    {
        $menu = Menu::where('slug', $slug)->firstOrFail();
        $menu->items()->delete();

        $this->insertItems($menu->id, null, $items);

        Cache::forget('menu.engine.all');
        Cache::forget("menu.engine.{$slug}");
    }

    private function insertItems(int $menuId, ?int $parentId, array $items, int &$i = 0): void
    {
        $menu = Menu::findOrFail($menuId);
        foreach ($items as $item) {
            $childItems = $item['children'] ?? [];
            unset($item['children']);

            $item['menu_id'] = $menuId;
            $item['parent_id'] = $parentId;
            $item['sort_order'] = $i++;
            $menuItem = $menu->items()->create($item);

            if (! empty($childItems)) {
                $this->insertItems($menuId, $menuItem->id, $childItems, $i);
            }
        }
    }
}
