<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\BorrowRecord;
use App\Models\Fine;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Fine>
 */
class FineFactory extends Factory
{
    public function definition(): array
    {
        return [
            'borrow_record_id' => BorrowRecord::factory(),
            'fine_type' => 'overdue',
            'fine_amount' => fake()->randomFloat(2, 1000, 50000),
            'paid_amount' => 0,
            'status' => 'unpaid',
            'payment_date' => null,
            'waived_by' => null,
            'notes' => null,
        ];
    }

    public function paid(): self
    {
        return $this->state(fn (array $attrs) => [
            'paid_amount' => $attrs['fine_amount'] ?? 1000,
            'status' => 'paid',
            'payment_date' => now()->toDateString(),
        ]);
    }

    public function waived(): self
    {
        return $this->state(fn (array $attrs) => [
            'status' => 'waived',
        ]);
    }
}
