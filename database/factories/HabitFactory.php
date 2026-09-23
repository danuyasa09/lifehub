<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class HabitFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->words(2, true),
            'icon' => fake()->randomElement(['🏋️', '📚', '💧', '🏃', '🧘']),
            'frequency' => fake()->randomElement(['daily', 'weekly']),
        ];
    }
}
