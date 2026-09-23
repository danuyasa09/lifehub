<?php

namespace Database\Seeders;

use App\Models\Habit;
use App\Models\HabitLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class HabitsSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        if (!$user) return;

        $habits = Habit::factory(3)->create(['user_id' => $user->id]);

        foreach ($habits as $habit) {
            for ($i = 0; $i < 30; $i++) {
                HabitLog::factory()->create([
                    'habit_id' => $habit->id,
                    'date' => Carbon::today()->subDays($i)->toDateString(),
                ]);
            }
        }
    }
}
