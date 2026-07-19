<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Theme;
use App\Services\ThemeEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class ThemeEngineTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_engine_has_name_and_version(): void
    {
        $engine = app(ThemeEngine::class);

        $this->assertSame('theme', $engine->name());
        $this->assertSame('1.0.0', $engine->version());
    }

    public function test_active_returns_null_when_no_active_theme(): void
    {
        Theme::factory()->create(['is_active' => false]);

        $engine = app(ThemeEngine::class);

        $this->assertNull($engine->active());
    }

    public function test_set_active_activates_theme(): void
    {
        $themeA = Theme::factory()->create(['slug' => 'theme-a', 'is_active' => true]);
        $themeB = Theme::factory()->create(['slug' => 'theme-b', 'is_active' => false]);

        $engine = app(ThemeEngine::class);
        $engine->setActive('theme-b');

        $this->assertTrue(Theme::where('slug', 'theme-b')->value('is_active'));
        $this->assertFalse(Theme::where('slug', 'theme-a')->value('is_active'));
        $this->assertSame('theme-b', $engine->active()->slug);
    }

    public function test_css_variables_generates_string(): void
    {
        $theme = Theme::factory()->create([
            'slug' => 'themed',
            'is_active' => true,
            'colors' => ['primary' => '#ff0000', 'secondary' => '#00ff00'],
        ]);

        $engine = app(ThemeEngine::class);
        $engine->setActive('themed');

        $css = $engine->cssVariables();

        $this->assertStringContainsString('--primary: #ff0000;', $css);
        $this->assertStringContainsString('--secondary: #00ff00;', $css);
    }

    public function test_css_variables_empty_when_no_active_theme(): void
    {
        $engine = app(ThemeEngine::class);

        $this->assertSame('', $engine->cssVariables());
    }

    public function test_fonts_returns_array(): void
    {
        Theme::factory()->create([
            'slug' => 'fonted',
            'is_active' => true,
            'fonts' => ['heading' => 'Inter', 'body' => 'Arial'],
        ]);

        $engine = app(ThemeEngine::class);
        $engine->setActive('fonted');

        $fonts = $engine->fonts();

        $this->assertSame('Inter', $fonts['heading']);
        $this->assertSame('Arial', $fonts['body']);
    }

    public function test_logo_returns_default_when_no_theme(): void
    {
        $engine = app(ThemeEngine::class);

        $this->assertStringContainsString('logo-default.png', $engine->logo());
    }

    public function test_favicon_returns_default_when_no_theme(): void
    {
        $engine = app(ThemeEngine::class);

        $this->assertStringContainsString('favicon.ico', $engine->favicon());
    }

    public function test_all_returns_cached_collection(): void
    {
        Theme::factory()->count(3)->create();

        $engine = app(ThemeEngine::class);
        $all = $engine->all();

        $this->assertSame(3, $all->count());
    }
}