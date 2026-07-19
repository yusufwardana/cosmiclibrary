<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Setting;
use App\Services\SettingEngine;
use Tests\TestCase;

class SettingEngineTest extends TestCase
{
    public function test_get_returns_default_when_missing(): void
    {
        $engine = app(SettingEngine::class);
        $this->assertNull($engine->get('nonexistent.key'));
        $this->assertSame('fallback', $engine->get('nonexistent.key', 'fallback'));
    }

    public function test_set_and_get(): void
    {
        $engine = app(SettingEngine::class);
        $engine->set('app.name', 'CosmicLib', 'string', 'general');

        $this->assertSame('CosmicLib', $engine->get('app.name'));
    }

    public function test_set_overwrites_value(): void
    {
        Setting::create(['key' => 'site.title', 'value' => 'Old', 'type' => 'string', 'group' => 'general']);

        $engine = app(SettingEngine::class);
        $engine->set('site.title', 'New Title');

        $this->assertSame('New Title', $engine->get('site.title'));
    }

    public function test_typed_value_integer(): void
    {
        $engine = app(SettingEngine::class);
        $engine->set('pagination.per_page', 25, 'integer', 'general');

        $this->assertSame(25, $engine->get('pagination.per_page'));
        $this->assertIsInt($engine->get('pagination.per_page'));
    }

    public function test_typed_value_boolean(): void
    {
        $engine = app(SettingEngine::class);
        $engine->set('app.debug', true, 'boolean', 'general');

        $this->assertTrue($engine->get('app.debug'));
    }

    public function test_typed_value_json(): void
    {
        $engine = app(SettingEngine::class);
        $engine->set('app.allowed_ips', ['192.168.1.1', '10.0.0.1'], 'json', 'general');

        $this->assertSame(['192.168.1.1', '10.0.0.1'], $engine->get('app.allowed_ips'));
    }

    public function test_group(): void
    {
        Setting::create(['key' => 'mail.host', 'value' => 'smtp.example.com', 'type' => 'string', 'group' => 'mail']);
        Setting::create(['key' => 'mail.port', 'value' => '587', 'type' => 'integer', 'group' => 'mail']);

        $engine = app(SettingEngine::class);
        $group = $engine->group('mail');

        $this->assertCount(2, $group);
    }

    public function test_is_installed(): void
    {
        $engine = app(SettingEngine::class);
        $this->assertFalse($engine->isInstalled());

        $engine->set('app.installed', true, 'boolean');
        $this->assertTrue($engine->isInstalled());
    }
}
