<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\NotificationTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationTemplateFactory extends Factory
{
    protected $model = NotificationTemplate::class;

    public function definition(): array
    {
        return [
            'slug' => $this->faker->unique()->word,
            'title' => $this->faker->sentence(3),
            'subject' => $this->faker->sentence(4),
            'body' => $this->faker->paragraph,
            'channel' => 'database',
            'is_active' => true,
        ];
    }
}