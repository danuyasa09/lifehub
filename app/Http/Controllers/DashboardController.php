<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // 1. Dynamic Greeting
        $hour = now()->hour;
        if ($hour < 12) {
            $greeting = 'Good Morning';
        } elseif ($hour < 17) {
            $greeting = 'Good Afternoon';
        } else {
            $greeting = 'Good Evening';
        }

        // 2. Today's Tasks
        $todayTasks = $user->tasks()
            ->with('category')
            ->whereDate('due_date', today())
            ->orderBy('due_date', 'asc')
            ->get();

        $pendingTasksCount = $todayTasks->whereIn('status', ['pending', 'in_progress'])->count();

        // 3. Active Projects
        $activeProjects = $user->projects()
            ->with('tasks')
            ->where('status', 'active')
            ->latest()
            ->get();

        // 4. Finance Summary (Current Month)
        $finances = $user->finances()
            ->whereMonth('transaction_date', today()->month)
            ->get();
            
        $financeSummary = [
            'income' => $finances->where('type', 'income')->sum('amount'),
            'expense' => $finances->where('type', 'expense')->sum('amount')
        ];

        // 5. Habit Streaks
        $habits = $user->habits()->with(['logs' => function($q) {
            $q->orderBy('date', 'desc');
        }])->get();

        $habitStreaks = [];
        $activeStreakCount = 0;
        foreach ($habits as $habit) {
            $streak = 0;
            $currentDate = Carbon::today();
            $todayCompleted = $habit->logs->firstWhere(fn($log) => $log->date->toDateString() === $currentDate->toDateString() && $log->is_completed) !== null;
            
            if (!$todayCompleted) {
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
            if ($streak > 0 || $todayCompleted) {
                $habitStreaks[] = [
                    'name' => $habit->name,
                    'streak' => $streak,
                    'icon' => $habit->icon,
                    'today' => $todayCompleted
                ];
                if ($streak >= 3) {
                    $activeStreakCount++;
                }
            }
        }

        // 6. AI Insight Message
        $insightMessages = [];
        if ($pendingTasksCount > 0) {
            $insightMessages[] = "You have $pendingTasksCount tasks due today.";
        } else {
            $insightMessages[] = "You've cleared your tasks for today. Great job!";
        }
        
        if ($activeStreakCount > 0) {
            $insightMessages[] = "You have $activeStreakCount habits on a streak of 3+ days. Keep the momentum going!";
        }

        $aiInsight = implode(" ", $insightMessages);

        return view('dashboard', compact(
            'greeting', 
            'todayTasks', 
            'activeProjects', 
            'financeSummary', 
            'habitStreaks', 
            'aiInsight'
        ));
    }
}
