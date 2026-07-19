<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\License;
use App\Services\LicenseEngine;
use Tests\TestCase;

class LicenseEngineTest extends TestCase
{
    public function test_engine_has_name_and_version(): void
    {
        $engine = app(LicenseEngine::class);
        $this->assertSame('license', $engine->name());
        $this->assertSame('1.0.0', $engine->version());
    }

    public function test_generate_creates_community_license(): void
    {
        $engine = app(LicenseEngine::class);
        $license = $engine->generate('admin@example.com', 'community', 'example.com');

        $this->assertDatabaseHas('licenses', [
            'license_key' => $license->license_key,
            'email' => 'admin@example.com',
            'edition' => 'community',
            'domain' => 'example.com',
            'status' => 'active',
        ]);
    }

    public function test_current_returns_null_when_none(): void
    {
        $this->assertNull(app(LicenseEngine::class)->current());
    }

    public function test_current_returns_active_license(): void
    {
        License::factory()->create(['status' => 'active', 'license_key' => 'A']);
        $this->assertNotNull(app(LicenseEngine::class)->current());
    }

    public function test_islicensed_returns_false_when_none(): void
    {
        $this->assertFalse(app(LicenseEngine::class)->isLicensed());
    }

    public function test_islicensed_returns_true_when_active(): void
    {
        License::factory()->create(['status' => 'active', 'expires_at' => null]);
        $this->assertTrue(app(LicenseEngine::class)->isLicensed());
    }

    public function test_islicensed_returns_false_when_expired(): void
    {
        License::factory()->create(['status' => 'active', 'expires_at' => now()->subDay()]);
        $this->assertFalse(app(LicenseEngine::class)->isLicensed());
    }

    public function test_suspend_changes_status(): void
    {
        $license = License::factory()->create(['status' => 'active']);
        $this->assertTrue(app(LicenseEngine::class)->suspend($license));
        $this->assertSame('suspended', $license->fresh()->status);
    }

    public function test_revoke_changes_status(): void
    {
        $license = License::factory()->create(['status' => 'active']);
        $this->assertTrue(app(LicenseEngine::class)->revoke($license));
        $this->assertSame('cancelled', $license->fresh()->status);
    }

    public function test_prune_expired_updates_status(): void
    {
        License::factory()->create(['status' => 'active', 'expires_at' => now()->subDay()]);
        License::factory()->create(['status' => 'active', 'expires_at' => null]);
        $count = app(LicenseEngine::class)->pruneExpired();
        $this->assertSame(1, $count);
    }

    public function test_activate_returns_null_when_http_fails(): void
    {
        \Illuminate\Support\Facades\Http::fake([
            '*' => \Illuminate\Support\Facades\Http::response(null, 500),
        ]);

        $result = app(LicenseEngine::class)->activate('KEY-123', 'admin@example.com', 'https://api.example.com/validate');
        $this->assertNull($result);
    }

    public function test_activate_returns_null_when_api_reports_invalid(): void
    {
        \Illuminate\Support\Facades\Http::fake([
            '*' => \Illuminate\Support\Facades\Http::response(['valid' => false], 200),
        ]);

        $result = app(LicenseEngine::class)->activate('KEY-123', 'admin@example.com', 'https://api.example.com/validate');
        $this->assertNull($result);
    }

    public function test_activate_creates_license_when_valid(): void
    {
        \Illuminate\Support\Facades\Http::fake([
            '*' => \Illuminate\Support\Facades\Http::response([
                'valid' => true,
                'customer_name' => 'John Doe',
                'product' => 'cosmiclib-library',
                'edition' => 'enterprise',
                'expires_at' => now()->addYear()->toDateTimeString(),
                'meta' => ['foo' => 'bar'],
            ], 200),
        ]);

        $result = app(LicenseEngine::class)->activate('KEY-123', 'admin@example.com', 'https://api.example.com/validate', 'example.com');
        $this->assertNotNull($result);
        $this->assertSame('active', $result->status);
        $this->assertSame('enterprise', $result->edition);
        $this->assertSame('John Doe', $result->customer_name);
    }

    public function test_validate_returns_false_for_invalid_local_license(): void
    {
        $license = License::factory()->create(['status' => 'suspended']);
        $this->assertFalse(app(LicenseEngine::class)->validate($license));
    }

    public function test_validate_updates_last_validated_when_local_only(): void
    {
        $license = License::factory()->create(['status' => 'active']);
        $this->assertTrue(app(LicenseEngine::class)->validate($license));
        $this->assertNotNull($license->fresh()->last_validated_at);
    }

    public function test_validate_performs_remote_check_successfully(): void
    {
        \Illuminate\Support\Facades\Http::fake([
            '*' => \Illuminate\Support\Facades\Http::response(['valid' => true, 'meta' => ['baz' => 'qux']], 200),
        ]);

        $license = License::factory()->create(['status' => 'active', 'domain' => 'example.com']);
        $this->assertTrue(app(LicenseEngine::class)->validate($license, 'https://api.example.com/check'));
        $this->assertSame('active', $license->fresh()->status);
    }

    public function test_validate_performs_remote_check_and_suspends_license_on_failure(): void
    {
        \Illuminate\Support\Facades\Http::fake([
            '*' => \Illuminate\Support\Facades\Http::response(['valid' => false], 200),
        ]);

        $license = License::factory()->create(['status' => 'active', 'domain' => 'example.com']);
        $this->assertFalse(app(LicenseEngine::class)->validate($license, 'https://api.example.com/check'));
        $this->assertSame('suspended', $license->fresh()->status);
    }
}
