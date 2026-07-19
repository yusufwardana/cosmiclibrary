<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\BookItem;
use App\Models\BorrowRecord;
use App\Models\Member;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<BorrowRecord>
 */
class BorrowRecordFactory extends Factory
{
    public function definition(): array
    {
        $borrowDate = Carbon::today()->subDays(7);

        return [
            'member_id' => Member::factory(),
            'book_item_id' => BookItem::factory(),
            'librarian_out_id' => User::factory(),
            'librarian_in_id' => null,
            'borrow_date' => $borrowDate,
            'due_date' => $borrowDate->copy()->addDays(7),
            'return_date' => null,
            'extend_count' => 0,
            'status' => 'borrowed',
            'notes' => null,
        ];
    }

    public function overdue(): self
    {
        return $this->state(fn (array $attrs) => [
            'due_date' => Carbon::today()->subDays(3),
            'status' => 'overdue',
        ]);
    }

    public function returned(): self
    {
        return $this->state(fn (array $attrs) => [
            'return_date' => Carbon::today(),
            'librarian_in_id' => User::factory(),
            'status' => 'returned',
        ]);
    }
}
