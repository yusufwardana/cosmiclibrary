<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Services\InstallerEngine;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class InstallerEngineTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $lockFile = config('installer.lock_file');
        if (File::exists($lockFile)) {
            File::delete($lockFile);
        }
    }

    protected function tearDown(): void
    {
        $lockFile = config('installer.lock_file');
        if (File::exists($lockFile)) {
            File::delete($lockFile);
        }

        parent::tearDown();
    }

    public function test_engine_has_name_and_version(): void
    {
        $engine = app(InstallerEngine::class);

        $this->assertSame('installer', $engine->name());
        $this->assertSame('1.0.0', $engine->version());
    }

    public function test_is_installed_returns_true_when_lock_file_exists(): void
    {
        File::put(config('installer.lock_file'), 'installed');

        $engine = app(InstallerEngine::class);

        $this->assertTrue($engine->isInstalled());
    }

    public function test_is_installed_returns_false_when_lock_file_missing(): void
    {
        $engine = app(InstallerEngine::class);

        $this->assertFalse($engine->isInstalled());
    }

    public function test_get_requirements_structure(): void
    {
        $engine = app(InstallerEngine::class);
        $requirements = $engine->getRequirements();

        $this->assertArrayHasKey('php', $requirements);
        $this->assertArrayHasKey('extensions', $requirements);
        $this->assertArrayHasKey('writable', $requirements);

        $this->assertIsBool($requirements['php']);
        $this->assertIsArray($requirements['extensions']);
        $this->assertIsArray($requirements['writable']);

        foreach ($requirements['extensions'] as $loaded) {
            $this->assertIsBool($loaded);
        }

        foreach ($requirements['writable'] as $isWritable) {
            $this->assertIsBool($isWritable);
        }
    }
}