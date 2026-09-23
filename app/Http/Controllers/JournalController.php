<?php

namespace App\Http\Controllers;

use App\Models\Journal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class JournalController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->input('date', Carbon::today()->toDateString());
        $carbonDate = Carbon::parse($date);
        
        $journal = $this->user()->journals()->where('date', $date)->first();
        
        if (!$journal) {
            $journal = new Journal([
                'date' => $date,
                'mood' => null,
                'what_happened' => '',
                'gratitude' => '',
                'lessons_learned' => '',
            ]);
        }

        return view('journals.index', compact('journal', 'carbonDate'));
    }

    public function history()
    {
        $journals = $this->user()->journals()
            ->orderBy('date', 'desc')
            ->get()
            ->groupBy(function($item) {
                return Carbon::parse($item->date)->format('F Y');
            });

        return view('journals.history', compact('journals'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'mood' => 'nullable|string',
            'what_happened' => 'nullable|string',
            'gratitude' => 'nullable|string',
            'lessons_learned' => 'nullable|string',
        ]);

        $journal = $this->user()->journals()->updateOrCreate(
            ['date' => $validated['date']],
            [
                'mood' => $validated['mood'],
                'what_happened' => $validated['what_happened'],
                'gratitude' => $validated['gratitude'],
                'lessons_learned' => $validated['lessons_learned'],
            ]
        );

        return redirect()->route('journals.index', ['date' => $validated['date']])
            ->with('success', 'Journal entry saved successfully.');
    }
    
    public function update(Request $request, Journal $journal)
    {
        if ($journal->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'mood' => 'nullable|string',
            'what_happened' => 'nullable|string',
            'gratitude' => 'nullable|string',
            'lessons_learned' => 'nullable|string',
        ]);

        $journal->update($validated);

        return redirect()->route('journals.index', ['date' => \Carbon\Carbon::parse($journal->date)->toDateString()])
            ->with('success', 'Journal entry updated successfully.');
    }
}
