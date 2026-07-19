<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Book>
 */
class BookFactory extends Factory
{
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'title' => fake()->sentence(4),
            'isbn' => fake()->unique()->isbn13(),
            'author' => fake()->name(),
            'publisher' => fake()->company(),
            'publish_year' => fake()->year(),
            'edition' => fake()->optional()->word(),
            'language' => 'id',
            'pages' => fake()->optional()->numberBetween(50, 500),
            'ddc_classification' => fake()->optional()->numerify('###.###'),
            'description' => fake()->optional()->paragraph(),
            'cover_image' => null,
            'total_copies' => 1,
            'available_copies' => 1,
        ];
    }
}
