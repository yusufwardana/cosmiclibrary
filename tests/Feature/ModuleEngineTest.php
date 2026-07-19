<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Module;
use App\Services\ModuleEngine;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class ModuleEngineTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Cache::forget('module.engine.all');

        $path = base_path('modules/_test/module.json');
        if (! is_dir(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }
        file_put_contents($path, json_encode([
            'name' => 'Test Module',
            'version' => '2.0.0',
            'description' => 'Test desc',
            'enabled' => true,
            'priority' => 50,
        ]));
    }

    public function test_name_and_version(): void
    {
        $engine = app(ModuleEngine::class);
        $this->assertSame('module', $engine->name());
        $this->assertSame('1.0.0', $engine->version());
    }

    public function test_discover_syncs_to_db(): void
    {
        $engine = app(ModuleEngine::class);
        $engine->discover();

        $module = $engine->get('_test');
        $this->assertNotNull($module);
        $this->assertSame('Test Module', $module->name);
        $this->assertSame('2.0.0', $module->version);
        $this->assertSame('active', $module->status);
        $this->assertSame(50, $module->priority);
    }

    public function test_has(): void
    {
        $engine = app(ModuleEngine::class);
        $engine->discover();

        $this->assertTrue($engine->has('_test'));
        $this->assertFalse($engine->has('nonexistent'));
    }

    public function test_get_returns_null_for_missing(): void
    {
        $engine = app(ModuleEngine::class);
        $this->assertNull($engine->get('nope'));
    }

    public function test_enable_disable_lifecycle(): void
    {
        $engine = app(ModuleEngine::class);
        $engine->discover();

        $engine->disable('_test');
        $this->assertSame('installed', $engine->get('_test')->status);
        $this->assertFalse($engine->active()->contains('slug', '_test'));

        $engine->enable('_test');
        $this->assertSame('active', $engine->get('_test')->status);
        $this->assertTrue($engine->active()->contains('slug', '_test'));
    }

    public function test_enable_missing_returns_false(): void
    {
        $engine = app(ModuleEngine::class);
        $this->assertFalse($engine->enable('ghost'));
    }

    public function test_remove_deletes_record(): void
    {
        $engine = app(ModuleEngine::class);
        $engine->discover();

        $this->assertTrue($engine->remove('_test'));
        $this->assertFalse($engine->has('_test'));
    }

    public function test_install_from_filesystem(): void
    {
        $engine = app(ModuleEngine::class);
        $this->assertTrue($engine->install('_test'));
        $this->assertSame('installed', $engine->get('_test')->status);
    }

    public function test_install_missing_returns_false(): void
    {
        $engine = app(ModuleEngine::class);
        $this->assertFalse($engine->install('ghost'));
    }

    public function test_dependencies_check_blocks_enable(): void
    {
        Module::factory()->create([
            'slug' => 'depmod',
            'dependencies' => ['missing-dep'],
            'status' => 'installed',
        ]);

        $engine = app(ModuleEngine::class);
        Cache::forget('module.engine.all');

        $this->assertFalse($engine->enable('depmod'));
        $this->assertSame('installed', $engine->get('depmod')->status);
    }

    public function test_dependencies_satisfied_allows_enable(): void
    {
        Module::factory()->active()->create(['slug' => 'core-dep']);
        Module::factory()->create([
            'slug' => 'depmod',
            'dependencies' => ['core-dep'],
            'status' => 'installed',
        ]);

        $engine = app(ModuleEngine::class);
        Cache::forget('module.engine.all');

        $this->assertTrue($engine->enable('depmod'));
        $this->assertSame('active', $engine->get('depmod')->status);
    }
}