<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Permission;
use App\Services\MenuEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class MenuEngineTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_engine_has_name_and_version(): void
    {
        $engine = app(MenuEngine::class);

        $this->assertSame('menu', $engine->name());
        $this->assertSame('1.0.0', $engine->version());
    }

    public function test_define_creates_menu(): void
    {
        $engine = app(MenuEngine::class);
        $menu = $engine->define('admin', 'Administrator', 'Menu admin');

        $this->assertInstanceOf(Menu::class, $menu);
        $this->assertSame('admin', $menu->slug);
        $this->assertSame('Administrator', $menu->name);
        $this->assertSame('Menu admin', $menu->description);
    }

    public function test_define_updates_existing_menu(): void
    {
        $engine = app(MenuEngine::class);
        $engine->define('admin', 'Old Name');
        $menu = $engine->define('admin', 'New Name', 'Updated desc');

        $this->assertSame(1, Menu::where('slug', 'admin')->count());
        $this->assertSame('New Name', $menu->name);
        $this->assertSame('Updated desc', $menu->description);
    }

    public function test_get_returns_menu_by_slug(): void
    {
        $engine = app(MenuEngine::class);
        $engine->define('admin', 'Admin Menu');

        $menu = $engine->get('admin');

        $this->assertNotNull($menu);
        $this->assertSame('admin', $menu->slug);
    }

    public function test_get_returns_null_for_unknown_slug(): void
    {
        $engine = app(MenuEngine::class);

        $this->assertNull($engine->get('nonexistent'));
    }

    public function test_sync_items_creates_nested_items(): void
    {
        $engine = app(MenuEngine::class);
        $engine->define('admin', 'Admin');

        $engine->syncItems('admin', [
            ['title' => 'Dashboard', 'url' => '/admin'],
            [
                'title' => 'Users',
                'url' => '/admin/users',
                'children' => [
                    ['title' => 'Create User', 'url' => '/admin/users/create'],
                    ['title' => 'Edit User', 'url' => '/admin/users/edit'],
                ],
            ],
        ]);

        $menu = $engine->get('admin');
        $items = $menu->items()->get();

        $this->assertSame(2, $items->where('parent_id', null)->count());
        $parent = $items->firstWhere('title', 'Users');
        $this->assertSame(2, MenuItem::where('parent_id', $parent->id)->count());
    }

    public function test_sync_items_replaces_existing_items(): void
    {
        $engine = app(MenuEngine::class);
        $engine->define('admin', 'Admin');

        $engine->syncItems('admin', [
            ['title' => 'Old Item', 'url' => '/old'],
        ]);

        $this->assertSame(1, MenuItem::count());

        $engine->syncItems('admin', [
            ['title' => 'New Item A', 'url' => '/a'],
            ['title' => 'New Item B', 'url' => '/b'],
        ]);

        $this->assertSame(2, MenuItem::count());
        $this->assertSame(0, MenuItem::where('title', 'Old Item')->count());
    }

    public function test_tree_returns_hierarchy(): void
    {
        $engine = app(MenuEngine::class);
        $engine->define('admin', 'Admin');
        $engine->syncItems('admin', [
            ['title' => 'Dashboard', 'url' => '/dashboard'],
            [
                'title' => 'Settings',
                'url' => '/settings',
                'children' => [
                    ['title' => 'General', 'url' => '/settings/general'],
                ],
            ],
        ]);

        $tree = $engine->tree('admin');

        $this->assertSame(2, $tree->count());
        $settings = $tree->firstWhere('title', 'Settings');
        $this->assertCount(1, $settings->children);
    }

    public function test_tree_returns_empty_for_nonexistent_menu(): void
    {
        $engine = app(MenuEngine::class);

        $this->assertTrue($engine->tree('nonexistent')->isEmpty());
    }

    public function test_tree_filters_by_permissions(): void
    {
        $permission = Permission::create([
            'name' => 'Manage Users',
            'slug' => 'manage_users',
            'module' => 'admin',
        ]);

        $engine = app(MenuEngine::class);
        $engine->define('admin', 'Admin');
        $engine->syncItems('admin', [
            ['title' => 'Public Page', 'url' => '/public'],
            ['title' => 'Protected Page', 'url' => '/protected', 'permission' => 'manage_users'],
        ]);

        // without permission - only public page
        $tree = $engine->tree('admin', collect());
        $this->assertSame(1, $tree->count());
        $this->assertSame('Public Page', $tree->first()->title);

        // with permission - both pages
        $tree = $engine->tree('admin', collect([$permission]));
        $this->assertSame(2, $tree->count());
    }

    public function test_tree_includes_items_without_permission_when_permissions_null(): void
    {
        $engine = app(MenuEngine::class);
        $engine->define('admin', 'Admin');
        $engine->syncItems('admin', [
            ['title' => 'Item A', 'url' => '/a'],
            ['title' => 'Item B', 'url' => '/b', 'permission' => 'some_perm'],
        ]);

        $tree = $engine->tree('admin', null);

        // Only item without permission
        $this->assertSame(1, $tree->count());
        $this->assertSame('Item A', $tree->first()->title);
    }

    public function test_define_clears_cache(): void
    {
        $engine = app(MenuEngine::class);
        $engine->define('admin', 'Admin');

        // Prime cache
        $engine->all();
        $this->assertTrue(Cache::has('menu.engine.all'));

        $engine->define('admin', 'Updated Admin');
        $this->assertFalse(Cache::has('menu.engine.all'));
    }
}