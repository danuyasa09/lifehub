<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class JournalFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'date' => fake()->date(),
            'mood' => fake()->randomElement(['happy', 'neutral', 'sad', 'excited', 'tired']),
            'what_happened' => fake()->paragraph(),
            'gratitude' => fake()->sentence(),
            'lessons_learned' => fake()->sentence(),
        ];
    }
}
