<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\License;
use Illuminate\Database\Eloquent\Factories\Factory;

class LicenseFactory extends Factory
{
    protected $model = License::class;

    public function definition(): array
    {
        return [
            'license_key' => 'CLB-'.strtoupper(fake()->regexify('[A-Z0-9]{4}')).'-'.strtoupper(fake()->regexify('[A-Z0-9]{4}')).'-'.strtoupper(fake()->regexify('[A-Z0-9]{4}')),
            'domain' => fake()->domainName(),
            'email' => fake()->safeEmail(),
            'customer_name' => fake()->name(),
            'product' => 'cosmiclib-library',
            'edition' => fake()->randomElement(['community', 'pro', 'enterprise']),
            'status' => 'active',
            'activated_at' => now(),
            'expires_at' => null,
            'last_validated_at' => now(),
            'meta' => null,
        ];
    }
}