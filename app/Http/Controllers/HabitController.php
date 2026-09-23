<?php

namespace App\Http\Controllers;

use App\Models\Habit;
use App\Models\HabitLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class HabitController extends Controller
{
    public function index()
    {
        $habits = $this->user()->habits()->with(['logs' => function ($query) {
            $query->orderBy('date', 'desc');
        }])->get();

        $today = Carbon::today()->toDateString();
        
        foreach ($habits as $habit) {
            $habit->today_completed = $habit->logs->firstWhere(fn($log) => $log->date->toDateString() === $today && $log->is_completed) !== null;
            
            // Calculate streak
            $streak = 0;
            $currentDate = Carbon::today();
            
            // Start checking from yesterday if today is not completed, else start from today
            if (!$habit->today_completed) {
                $currentDate->subDay();
            }

            while (true) {
                $log = $habit->logs->firstWhere(fn($l) => $l->date->toDateString() === $currentDate->toDateString());
                if ($log && $log->is_completed) {
                    $streak++;
                    $currentDate->subDay();
                } else {
                    break;
                }
            }
            
            $habit->current_streak = $streak;
        }

        return view('habits.index', compact('habits'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:50',
            'frequency' => 'required|in:daily,weekly,monthly',
        ]);

        $this->user()->habits()->create($validated);

        return redirect()->route('habits.index')->with('success', 'Habit created successfully.');
    }

    public function destroy(Habit $habit)
    {
        if ($habit->user_id !== Auth::id()) {
            abort(403);
        }

        $habit->delete();

        return redirect()->route('habits.index')->with('success', 'Habit deleted successfully.');
    }

    public function toggle(Request $request, Habit $habit)
    {
        if ($habit->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $date = $request->input('date', Carbon::today()->toDateString());
        
        $log = $habit->logs()->firstOrNew(['date' => $date]);
        $log->is_completed = !$log->is_completed;
        $log->save();

        $xpGained = 0;
        if ($log->is_completed) {
            $request->user()->addExperience(5);
            $xpGained = 5;
        }

        return response()->json(['success' => true, 'is_completed' => $log->is_completed, 'xp_gained' => $xpGained]);
    }
}
