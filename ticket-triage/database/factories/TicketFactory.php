<?php

namespace Database\Factories;

use App\Models\Ticket;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    public function definition(): array
    {
        return [
            'subject' => $this->faker->sentence(6, true),
            'body' => $this->faker->paragraph(3, true),
            'status' => $this->faker->randomElement(['open','in_progress','closed']),
            'category' => $this->faker->optional()->randomElement(['billing','technical','general']),
            'note' => $this->faker->optional()->sentence(),
            'confidence' => $this->faker->optional()->randomFloat(2, 0.5, 1.0),
            'explanation' => $this->faker->optional()->sentence(12),
        ];
    }
}