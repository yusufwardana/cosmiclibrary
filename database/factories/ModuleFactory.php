<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ModuleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'slug' => $this->faker->unique()->slug(2),
            'name' => $this->faker->words(2, true),
            'version' => '1.0.0',
            'description' => $this->faker->sentence(),
            'provider' => null,
            'priority' => 100,
            'dependencies' => null,
            'compatibility' => null,
            'status' => 'installed',
        ];
    }

    public function active(): static
    {
        return $this->state(['status' => 'active']);
    }
}