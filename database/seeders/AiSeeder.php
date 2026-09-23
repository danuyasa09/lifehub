<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Notifications\DummyNotification;

class AiSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        if (!$user) return;

        // Create a dummy notification
        $user->notify(new DummyNotification('You have 3 tasks due tomorrow.'));
        $user->notify(new DummyNotification('Great job! You hit your habit goals for this week.'));

        // Create some sample AI chats
        $chat1 = $user->aiChats()->create(['title' => 'Project Management Help']);
        $chat1->messages()->create(['role' => 'user', 'content' => 'How should I organize my upcoming project launch?']);
        $chat1->messages()->create(['role' => 'assistant', 'content' => "I recommend breaking it down into these phases:\n1. **Planning**: Define goals and timeline.\n2. **Execution**: Assign tasks to team members.\n3. **Review**: Ensure everything meets quality standards.\n\nWould you like me to create a project template for you?"]);

        $chat2 = $user->aiChats()->create(['title' => 'Weekly Review']);
        $chat2->messages()->create(['role' => 'user', 'content' => 'Give me a summary of my tasks this week.']);
        $chat2->messages()->create(['role' => 'assistant', 'content' => 'You completed 14 tasks this week! You have 3 pending tasks related to "Marketing".']);
    }
}
