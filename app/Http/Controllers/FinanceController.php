<?php

namespace App\Http\Controllers;

use App\Models\Finance;
use Illuminate\Http\Request;

class FinanceController extends Controller
{
    public function index(Request $request)
    {
        $query = $this->user()->finances();

        if ($request->has('month') && $request->month != '') {
            $query->whereMonth('transaction_date', $request->month);
        }
        if ($request->has('type') && $request->type != '') {
            $query->where('type', $request->type);
        }

        $transactions = $query->latest('transaction_date')->paginate(15);
        
        $totalIncome = $this->user()->finances()->where('type', 'income')->sum('amount');
        $totalExpense = $this->user()->finances()->where('type', 'expense')->sum('amount');
        $totalSavings = $this->user()->finances()->where('type', 'savings')->sum('amount');
        $currentBalance = $totalIncome - $totalExpense;

        return view('finance.index', compact('transactions', 'totalIncome', 'totalExpense', 'totalSavings', 'currentBalance'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:income,expense,savings',
            'amount' => 'required|numeric|min:0',
            'category' => 'required|string',
            'description' => 'nullable|string',
            'transaction_date' => 'required|date',
            'is_recurring' => 'required|in:none,daily,weekly,monthly',
        ]);

        $this->user()->finances()->create($validated);

        return redirect()->route('finances.index')->with('success', 'Transaction added successfully.');
    }
}
