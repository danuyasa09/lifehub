<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index()
    {
        $user = $this->user();

        // 1. Productivity (Task Completion)
        $tasksData = DB::table('tasks')
            ->select('status', DB::raw('count(*) as count'))
            ->where('user_id', $user->id)
            ->groupBy('status')
            ->get();
        $taskStats = ['completed' => 0, 'pending' => 0];
        foreach($tasksData as $t) {
            if ($t->status === 'completed') {
                $taskStats['completed'] += $t->count;
            } else {
                $taskStats['pending'] += $t->count;
            }
        }

        // 2. Expenses by category
        $expensesData = DB::table('finances')
            ->select('category', DB::raw('sum(amount) as total'))
            ->where('user_id', $user->id)
            ->where('type', 'expense')
            ->groupBy('category')
            ->get();
            
        $expenseLabels = $expensesData->pluck('category')->toArray();
        $expenseValues = $expensesData->pluck('total')->toArray();

        // 3. Habits (last 30 days)
        $thirtyDaysAgo = now()->subDays(30)->toDateString();
        $habitsData = DB::table('habit_logs')
            ->join('habits', 'habit_logs.habit_id', '=', 'habits.id')
            ->select('habit_logs.date', DB::raw('count(*) as count'))
            ->where('habits.user_id', $user->id)
            ->where('habit_logs.date', '>=', $thirtyDaysAgo)
            ->groupBy('habit_logs.date')
            ->orderBy('habit_logs.date')
            ->get();
            
        $habitDates = $habitsData->pluck('date')->toArray();
        $habitCounts = $habitsData->pluck('count')->toArray();

        // 4. Mood distribution (Journals)
        $moodData = DB::table('journals')
            ->select('mood', DB::raw('count(*) as count'))
            ->where('user_id', $user->id)
            ->whereNotNull('mood')
            ->groupBy('mood')
            ->get();
            
        $moodLabels = $moodData->pluck('mood')->toArray();
        $moodValues = $moodData->pluck('count')->toArray();

        return view('analytics.index', compact(
            'taskStats', 
            'expenseLabels', 'expenseValues', 
            'habitDates', 'habitCounts',
            'moodLabels', 'moodValues'
        ));
    }
}
