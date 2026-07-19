<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Widget;
use App\Services\WidgetEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Tests\TestCase;

class WidgetEngineTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_engine_has_name_and_version(): void
    {
        $engine = app(WidgetEngine::class);

        $this->assertSame('widget', $engine->name());
        $this->assertSame('1.0.0', $engine->version());
    }

    public function test_register_creates_widget(): void
    {
        $engine = app(WidgetEngine::class);
        $widget = $engine->register('stats', [
            'name' => 'Statistics',
            'area' => 'dashboard',
            'view' => 'widgets.stats',
            'sort_order' => 10,
        ]);

        $this->assertInstanceOf(Widget::class, $widget);
        $this->assertSame('stats', $widget->slug);
        $this->assertSame('Statistics', $widget->name);
        $this->assertSame('dashboard', $widget->area);
    }

    public function test_register_updates_existing_widget(): void
    {
        $engine = app(WidgetEngine::class);
        $engine->register('stats', ['name' => 'Old']);
        $widget = $engine->register('stats', ['name' => 'New Stats']);

        $this->assertSame(1, Widget::where('slug', 'stats')->count());
        $this->assertSame('New Stats', $widget->name);
    }

    public function test_has_checks_slug_exists(): void
    {
        $engine = app(WidgetEngine::class);

        $this->assertFalse($engine->has('stats'));

        $engine->register('stats', ['name' => 'Stats']);

        $this->assertTrue($engine->has('stats'));
    }

    public function test_all_returns_cached_collection(): void
    {
        $engine = app(WidgetEngine::class);
        $engine->register('widget_a', ['name' => 'A', 'sort_order' => 2]);
        $engine->register('widget_b', ['name' => 'B', 'sort_order' => 1]);

        $all = $engine->all();

        $this->assertSame(2, $all->count());
        // Sorted by sort_order: B (1) before A (2)
        $this->assertSame('widget_b', $all->first()->slug);
    }

    public function test_area_filters_by_area_and_active(): void
    {
        $engine = app(WidgetEngine::class);
        $engine->register('sidebar_a', ['name' => 'A', 'area' => 'sidebar']);
        $engine->register('sidebar_b', ['name' => 'B', 'area' => 'sidebar', 'is_active' => false]);
        $engine->register('dashboard_c', ['name' => 'C', 'area' => 'dashboard']);

        $sidebar = $engine->area('sidebar');

        $this->assertSame(1, $sidebar->count());
        $this->assertSame('sidebar_a', $sidebar->first()->slug);
    }

    public function test_enable_and_disable(): void
    {
        $engine = app(WidgetEngine::class);
        $engine->register('stats', ['name' => 'Stats', 'is_active' => true]);

        $engine->disable('stats');
        $this->assertFalse(Widget::where('slug', 'stats')->value('is_active'));

        $engine->enable('stats');
        $this->assertTrue(Widget::where('slug', 'stats')->value('is_active'));
    }

    public function test_render_returns_html_for_area(): void
    {
        // Register a fake view
        View::addNamespace('test-widgets', __DIR__ . '/../fixtures/widgets');
        @mkdir(__DIR__ . '/../fixtures/widgets', 0755, true);
        file_put_contents(
            __DIR__ . '/../fixtures/widgets/test.blade.php',
            '<div>{{ $widget->name }}</div>'
        );

        $engine = app(WidgetEngine::class);
        $engine->register('test', [
            'name' => 'Test Widget',
            'area' => 'sidebar',
            'view' => 'test-widgets::test',
        ]);

        $html = $engine->render('sidebar');

        $this->assertStringContainsString('Test Widget', $html);

        // cleanup
        @unlink(__DIR__ . '/../fixtures/widgets/test.blade.php');
        @rmdir(__DIR__ . '/../fixtures/widgets');
        @rmdir(__DIR__ . '/../fixtures');
    }

    public function test_render_returns_empty_string_when_no_widgets(): void
    {
        $engine = app(WidgetEngine::class);

        $this->assertSame('', $engine->render('nonexistent'));
    }

    public function test_render_skips_missing_views(): void
    {
        $engine = app(WidgetEngine::class);
        $engine->register('broken', [
            'name' => 'Broken',
            'area' => 'sidebar',
            'view' => 'nonexistent.view.name',
        ]);

        $html = $engine->render('sidebar');

        $this->assertSame('', $html);
    }

    public function test_register_clears_cache(): void
    {
        $engine = app(WidgetEngine::class);
        $engine->register('stats', ['name' => 'Stats']);

        // Prime cache
        $engine->all();
        $this->assertTrue(Cache::has('widget.engine.all'));

        $engine->register('stats', ['name' => 'Updated Stats']);
        $this->assertFalse(Cache::has('widget.engine.all'));
    }
}