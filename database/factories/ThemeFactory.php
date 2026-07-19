<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Theme;
use Illuminate\Database\Eloquent\Factories\Factory;

class ThemeFactory extends Factory
{
    protected $model = Theme::class;

    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'slug' => fake()->slug(),
            'description' => fake()->sentence(),
            'version' => '1.0.0',
            'author' => fake()->name(),
            'path' => 'themes/'.fake()->slug(),
            'colors' => ['primary' => '#000'],
            'fonts' => [],
            'is_active' => false,
        ];
    }
}
