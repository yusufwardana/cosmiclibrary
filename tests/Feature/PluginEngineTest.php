<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Plugin;
use App\Services\PluginEngine;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class PluginEngineTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Cache::forget('plugin.engine.all');

        $dir = base_path('plugins/_test');
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        file_put_contents($dir.'/plugin.json', json_encode([
            'name' => 'Test Plugin',
            'version' => '2.0.0',
            'description' => 'Test plugin desc',
            'author' => 'Tester',
            'hook' => 'library:after_borrow',
            'enabled' => true,
            'priority' => 50,
        ]));
    }

    public function test_name_and_version(): void
    {
        $engine = app(PluginEngine::class);
        $this->assertSame('plugin', $engine->name());
        $this->assertSame('1.0.0', $engine->version());
    }

    public function test_discover_syncs_to_db(): void
    {
        $engine = app(PluginEngine::class);
        $engine->discover();

        $plugin = $engine->get('_test');
        $this->assertNotNull($plugin);
        $this->assertSame('Test Plugin', $plugin->name);
        $this->assertSame('2.0.0', $plugin->version);
        $this->assertTrue($plugin->is_active);
        $this->assertSame(50, $plugin->priority);
        $this->assertSame('library:after_borrow', $plugin->hook);
    }

    public function test_has(): void
    {
        $engine = app(PluginEngine::class);
        $engine->discover();

        $this->assertTrue($engine->has('_test'));
        $this->assertFalse($engine->has('nonexistent'));
    }

    public function test_get_returns_null_for_missing(): void
    {
        $engine = app(PluginEngine::class);
        $this->assertNull($engine->get('nope'));
    }

    public function test_enable_disable_lifecycle(): void
    {
        $engine = app(PluginEngine::class);
        $engine->discover();

        $engine->disable('_test');
        $this->assertFalse($engine->get('_test')->is_active);
        $this->assertFalse($engine->active()->contains('slug', '_test'));

        $engine->enable('_test');
        $this->assertTrue($engine->get('_test')->is_active);
        $this->assertTrue($engine->active()->contains('slug', '_test'));
    }

    public function test_enable_missing_returns_false(): void
    {
        $engine = app(PluginEngine::class);
        $this->assertFalse($engine->enable('ghost'));
    }

    public function test_remove_deletes_record(): void
    {
        $engine = app(PluginEngine::class);
        $engine->discover();

        $this->assertTrue($engine->remove('_test'));
        $this->assertFalse($engine->has('_test'));
    }

    public function test_install_from_filesystem(): void
    {
        $engine = app(PluginEngine::class);
        $this->assertTrue($engine->install('_test'));
        $this->assertFalse($engine->get('_test')->is_active);
    }

    public function test_install_missing_returns_false(): void
    {
        $engine = app(PluginEngine::class);
        $this->assertFalse($engine->install('ghost'));
    }
}