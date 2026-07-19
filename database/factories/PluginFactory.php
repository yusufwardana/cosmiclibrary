<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PluginFactory extends Factory
{
    public function definition(): array
    {
        return [
            'slug' => $this->faker->unique()->slug(2),
            'name' => $this->faker->words(2, true),
            'version' => '1.0.0',
            'description' => $this->faker->sentence(),
            'author' => $this->faker->name(),
            'hook' => null,
            'settings' => null,
            'is_active' => false,
            'priority' => 100,
        ];
    }

    public function active(): static
    {
        return $this->state(['is_active' => true]);
    }
}