<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        $tasks = Task::with('category')
            ->where('user_id', Auth::id())
            ->whereNotNull('due_date')
            ->get();

        $categories = Auth::user()->taskCategories;

        return view('calendar.index', compact('tasks', 'categories'));
    }
}
