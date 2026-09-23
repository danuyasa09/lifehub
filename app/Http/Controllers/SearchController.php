<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Note;
use App\Models\Project;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('q');
        if (!$query) {
            return response()->json([]);
        }

        $user = auth()->user();
        
        $tasks = Task::where('user_id', $user->id)
            ->where('title', 'like', "%{$query}%")
            ->take(5)
            ->get()
            ->map(fn($t) => ['title' => $t->title, 'type' => 'Task', 'url' => route('tasks.index')]);

        $notes = Note::where('user_id', $user->id)
            ->where('title', 'like', "%{$query}%")
            ->take(5)
            ->get()
            ->map(fn($n) => ['title' => $n->title, 'type' => 'Note', 'url' => route('notes.edit', $n)]);

        $projects = Project::where('user_id', $user->id)
            ->where('name', 'like', "%{$query}%")
            ->take(5)
            ->get()
            ->map(fn($p) => ['title' => $p->name, 'type' => 'Project', 'url' => route('projects.show', $p)]);

        $results = collect()->concat($tasks)->concat($notes)->concat($projects);

        return response()->json($results);
    }
}
