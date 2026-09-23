<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $query = Task::with('category')->where('user_id', Auth::id());

        // Basic filtering for tabs (Today, Tomorrow, Upcoming, Completed)
        // This will be handled on frontend via Alpine or backend depending on implementation.
        // Let's pass all tasks for now, and filter on frontend for a smoother UI experience.
        $tasks = $query->orderBy('due_date', 'asc')->get();

        $categories = Auth::user()->taskCategories;

        return view('tasks.index', compact('tasks', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:task_categories,id',
            'parent_id' => 'nullable|exists:tasks,id',
            'priority' => 'required|in:low,medium,high,urgent',
            'status' => 'required|in:pending,in_progress,completed',
            'due_date' => 'nullable|date',
            'reminder_at' => 'nullable|date',
            'is_recurring' => 'required|in:none,daily,weekly,monthly',
            'progress' => 'required|integer|min:0|max:100',
        ]);

        $request->user()->tasks()->create($validated);

        return redirect()->back()->with('success', 'Task created successfully.');
    }

    public function update(Request $request, Task $task)
    {
        if ($task->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:task_categories,id',
            'parent_id' => 'nullable|exists:tasks,id',
            'priority' => 'required|in:low,medium,high,urgent',
            'status' => 'required|in:pending,in_progress,completed',
            'due_date' => 'nullable|date',
            'reminder_at' => 'nullable|date',
            'is_recurring' => 'required|in:none,daily,weekly,monthly',
            'progress' => 'required|integer|min:0|max:100',
        ]);

        $originalStatus = $task->status;
        $task->update($validated);

        if ($originalStatus !== 'completed' && $task->status === 'completed') {
            $request->user()->addExperience(10);
            session()->flash('xp_gained', 10);
        }

        return redirect()->back()->with('success', 'Task updated successfully.');
    }

    public function destroy(Task $task)
    {
        if ($task->user_id !== Auth::id()) {
            abort(403);
        }

        $task->delete();

        return redirect()->back()->with('success', 'Task deleted successfully.');
    }
}
