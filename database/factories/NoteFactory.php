<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class NoteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(),
            'content' => fake()->paragraphs(3, true),
            'folder' => fake()->randomElement(['Personal', 'Work', 'Ideas', null]),
            'is_favorite' => fake()->boolean(20),
            'is_archived' => fake()->boolean(10),
        ];
    }
}
