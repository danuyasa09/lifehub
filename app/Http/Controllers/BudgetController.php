<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|string',
            'limit_amount' => 'required|numeric|min:0',
            'period' => 'required|date',
        ]);

        $this->user()->budgets()->create($validated);

        return redirect()->back()->with('success', 'Budget set successfully.');
    }
}
