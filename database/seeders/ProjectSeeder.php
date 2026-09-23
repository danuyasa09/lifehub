<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\ProjectTask;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();
        if (!$user) {
            return;
        }

        Project::factory(3)->create(['user_id' => $user->id])->each(function ($project) {
            ProjectTask::factory(fake()->numberBetween(10, 15))->create([
                'project_id' => $project->id,
            ]);
        });
    }
}
