<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectTask;
use Illuminate\Http\Request;

class ProjectTaskController extends Controller
{
    public function store(Request $request, Project $project)
    {
        if ($project->user_id !== $this->user()->id) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:todo,doing,review,done',
        ]);

        // Get max order
        $maxOrder = $project->tasks()->where('status', $validated['status'])->max('order');
        $validated['order'] = $maxOrder !== null ? $maxOrder + 1 : 0;

        $project->tasks()->create($validated);

        return redirect()->back()->with('success', 'Task added successfully.');
    }

    public function updateStatus(Request $request, ProjectTask $task)
    {
        $project = $task->project;
        if ($project->user_id !== $this->user()->id) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => 'required|in:todo,doing,review,done',
            'order' => 'required|integer',
        ]);

        $task->update([
            'status' => $validated['status'],
            'order' => $validated['order']
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy(ProjectTask $task)
    {
        if ($task->project->user_id !== $this->user()->id) {
            abort(403);
        }

        $task->delete();

        return redirect()->back()->with('success', 'Task deleted successfully.');
    }
}
