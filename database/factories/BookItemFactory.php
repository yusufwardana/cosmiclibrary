<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Book;
use App\Models\BookItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BookItem>
 */
class BookItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'book_id' => Book::factory(),
            'barcode' => fake()->unique()->ean13(),
            'call_number' => fake()->numerify('###.###'),
            'shelf_location' => fake()->optional()->bothify('Rack-##'),
            'status' => 'available',
            'condition' => 'good',
        ];
    }
}
