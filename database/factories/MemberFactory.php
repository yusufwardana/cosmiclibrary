<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Member;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Member>
 */
class MemberFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => null,
            'member_number' => fake()->unique()->numerify('M-####'),
            'type' => fake()->randomElement(['student', 'teacher', 'staff']),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'class_name' => fake()->optional()->word(),
            'join_date' => now()->toDateString(),
            'photo' => null,
            'status' => 'active',
            'notes' => null,
        ];
    }
}
