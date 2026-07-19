<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Update;
use Illuminate\Database\Eloquent\Factories\Factory;

class UpdateFactory extends Factory
{
    protected $model = Update::class;

    public function definition(): array
    {
        return [
            'version' => fake()->numerify('1.#.##'),
            'channel' => fake()->randomElement(['stable', 'beta', 'alpha']),
            'release_url' => fake()->url(),
            'checksum' => hash('sha256', fake()->uuid()),
            'size_bytes' => fake()->numberBetween(1_000_000, 50_000_000),
            'changelog' => fake()->paragraphs(2, true),
            'status' => 'pending',
            'log' => null,
            'applied_at' => null,
        ];
    }
}