<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Update;
use App\Services\UpdateEngine;
use Tests\TestCase;

class UpdateEngineTest extends TestCase
{
    public function test_engine_has_name_and_version(): void
    {
        $engine = app(UpdateEngine::class);
        $this->assertSame('update', $engine->name());
        $this->assertSame('1.0.0', $engine->version());
    }

    public function test_pending_returns_empty_array_when_none(): void
    {
        $this->assertEmpty(app(UpdateEngine::class)->pending());
    }

    public function test_pending_returns_pending_updates(): void
    {
        Update::factory()->create(['status' => 'pending', 'version' => '1.1.0']);
        $pending = app(UpdateEngine::class)->pending();
        $this->assertCount(1, $pending);
        $this->assertSame('1.1.0', $pending[0]->version);
    }

    public function test_history_returns_applied_updates(): void
    {
        Update::factory()->create(['status' => 'applied', 'version' => '1.0.1', 'applied_at' => now()->subDay()]);
        Update::factory()->create(['status' => 'applied', 'version' => '1.0.2', 'applied_at' => now()]);
        $history = app(UpdateEngine::class)->history();
        $this->assertCount(2, $history);
        $this->assertSame('1.0.2', $history[0]->version);
    }

    public function test_rollback_returns_false_for_non_applied(): void
    {
        $update = Update::factory()->create(['status' => 'pending']);
        $this->assertFalse(app(UpdateEngine::class)->rollback($update));
    }

    public function test_rollback_returns_true_for_applied(): void
    {
        $update = Update::factory()->create(['status' => 'applied', 'applied_at' => now()]);
        $this->assertTrue(app(UpdateEngine::class)->rollback($update));
        $this->assertDatabaseHas('updates', ['id' => $update->id, 'status' => 'rolled_back']);
    }

    public function test_check_returns_null_when_no_update_available(): void
    {
        \Illuminate\Support\Facades\Http::fake([
            '*' => \Illuminate\Support\Facades\Http::response(['version' => '1.0.0'], 200),
        ]);

        $result = app(UpdateEngine::class)->check('https://api.example.com/update', '1.0.0');
        $this->assertNull($result);
    }

    public function test_check_returns_update_model_when_newer_version(): void
    {
        \Illuminate\Support\Facades\Http::fake([
            '*' => \Illuminate\Support\Facades\Http::response([
                'version' => '2.0.0',
                'url' => 'https://example.com/update.zip',
                'checksum' => 'abc123',
                'size' => 1024,
                'changelog' => 'New features',
            ], 200),
        ]);

        $result = app(UpdateEngine::class)->check('https://api.example.com/update', '1.0.0');
        $this->assertNotNull($result);
        $this->assertSame('2.0.0', $result->version);
        $this->assertSame('pending', $result->status);
    }

    public function test_check_returns_null_when_http_fails(): void
    {
        \Illuminate\Support\Facades\Http::fake([
            '*' => \Illuminate\Support\Facades\Http::response(null, 500),
        ]);

        $result = app(UpdateEngine::class)->check('https://api.example.com/update', '1.0.0');
        $this->assertNull($result);
    }

    public function test_download_returns_false_when_empty_release_url(): void
    {
        $update = Update::factory()->make(['release_url' => null]);
        $this->assertFalse(app(UpdateEngine::class)->download($update));
    }

    public function test_download_returns_false_when_http_fails(): void
    {
        \Illuminate\Support\Facades\Http::fake([
            '*' => \Illuminate\Support\Facades\Http::response(null, 404),
        ]);

        $update = Update::factory()->create(['release_url' => 'https://example.com/update.zip']);
        $this->assertFalse(app(UpdateEngine::class)->download($update));
        $this->assertSame('failed', $update->fresh()->status);
    }

    public function test_extract_returns_null_when_archive_missing(): void
    {
        $update = Update::factory()->create(['log' => null]);
        $this->assertNull(app(UpdateEngine::class)->extract($update));
    }

    public function test_apply_returns_false_when_path_missing(): void
    {
        $update = Update::factory()->create(['status' => 'pending']);
        $this->assertFalse(app(UpdateEngine::class)->apply($update, '/nonexistent/path'));
        $this->assertSame('failed', $update->fresh()->status);
    }
}
