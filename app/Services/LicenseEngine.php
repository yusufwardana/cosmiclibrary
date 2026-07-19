<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\License;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class LicenseEngine extends BaseService
{
    public function name(): string
    {
        return 'license';
    }

    public function version(): string
    {
        return '1.0.0';
    }

    /**
     * Generate license key locally (for offline/community installs).
     */
    public function generate(string $email, string $edition = 'community', ?string $domain = null): License
    {
        $key = 'CLB-'.strtoupper(Str::random(4)).'-'.strtoupper(Str::random(4)).'-'.strtoupper(Str::random(4));

        return License::create([
            'license_key' => $key,
            'email' => $email,
            'edition' => $edition,
            'domain' => $domain,
            'status' => 'active',
            'activated_at' => now(),
            'last_validated_at' => now(),
        ]);
    }

    /**
     * Activate license via remote validation endpoint.
     */
    public function activate(string $licenseKey, string $email, string $endpoint, ?string $domain = null): ?License
    {
        try {
            $response = Http::timeout(15)->post($endpoint, [
                'license_key' => $licenseKey,
                'email' => $email,
                'domain' => $domain ?? url('/'),
            ]);

            if (! $response->successful()) {
                $this->log('warning', "License activation failed: {$response->status()}");

                return null;
            }

            $data = $response->json();
            if (($data['valid'] ?? false) !== true) {
                return null;
            }

            return License::updateOrCreate(
                ['license_key' => $licenseKey],
                [
                    'email' => $email,
                    'domain' => $domain,
                    'customer_name' => $data['customer_name'] ?? null,
                    'product' => $data['product'] ?? 'cosmiclib-library',
                    'edition' => $data['edition'] ?? 'community',
                    'status' => 'active',
                    'activated_at' => now(),
                    'expires_at' => ! empty($data['expires_at']) ? $data['expires_at'] : null,
                    'last_validated_at' => now(),
                    'meta' => $data['meta'] ?? null,
                ]
            );
        } catch (\Throwable $e) {
            $this->log('error', 'License activation exception: '.$e->getMessage());

            return null;
        }
    }

    /**
     * Validate existing license (local + optional remote re-check).
     */
    public function validate(License $license, ?string $endpoint = null): bool
    {
        if (! $license->isValid()) {
            return false;
        }

        if ($endpoint) {
            try {
                $response = Http::timeout(10)->post($endpoint, [
                    'license_key' => $license->license_key,
                    'domain' => $license->domain,
                ]);

                if (! $response->successful()) {
                    return false;
                }

                $data = $response->json();
                $valid = ($data['valid'] ?? false) === true;

                $license->update([
                    'status' => $valid ? 'active' : 'suspended',
                    'last_validated_at' => now(),
                    'meta' => $data['meta'] ?? $license->meta,
                ]);

                return $valid;
            } catch (\Throwable $e) {
                $this->log('warning', 'License validation remote failed, keeping local status');

                return true;
            }
        }

        $license->update(['last_validated_at' => now()]);

        return true;
    }

    /**
     * Get active license.
     */
    public function current(): ?License
    {
        return License::active()->first();
    }

    /**
     * Check if any active license exists.
     */
    public function isLicensed(): bool
    {
        $license = $this->current();

        return $license && $license->isValid();
    }

    /**
     * Suspend a license.
     */
    public function suspend(License $license): bool
    {
        return $license->update(['status' => 'suspended']) > 0;
    }

    /**
     * Revoke/cancel a license.
     */
    public function revoke(License $license): bool
    {
        return $license->update(['status' => 'cancelled']) > 0;
    }

    /**
     * Expire outdated licenses.
     */
    public function pruneExpired(): int
    {
        return License::where('status', 'active')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->update(['status' => 'expired']);
    }
}