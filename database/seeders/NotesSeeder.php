<?php

namespace Database\Seeders;

use App\Models\Note;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;

class NotesSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        if (!$user) return;

        $tags = Tag::factory(5)->create(['user_id' => $user->id]);

        Note::factory(10)->create([
            'user_id' => $user->id,
        ])->each(function ($note) use ($tags) {
            $note->tags()->attach(
                $tags->random(rand(1, 3))->pluck('id')->toArray()
            );
        });
    }
}
