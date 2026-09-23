<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $categories = \App\Models\TaskCategory::factory(4)->create([
            'user_id' => $user->id,
        ]);

        \App\Models\Task::factory(20)->create([
            'user_id' => $user->id,
            'category_id' => fn () => $categories->random()->id,
        ]);

        $this->call([
            NotesSeeder::class,
            HabitsSeeder::class,
            JournalsSeeder::class,
            ProjectSeeder::class,
            FinanceSeeder::class,
            AiSeeder::class,
        ]);
    }
}
