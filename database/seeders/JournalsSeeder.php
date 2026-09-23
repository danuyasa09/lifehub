<?php

namespace Database\Seeders;

use App\Models\Journal;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class JournalsSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        if (!$user) return;

        for ($i = 0; $i < 5; $i++) {
            Journal::factory()->create([
                'user_id' => $user->id,
                'date' => Carbon::today()->subDays($i)->toDateString(),
            ]);
        }
    }
}
