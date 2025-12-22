<?php

namespace App\Http\Controllers;

use App\Models\RecurringIncome;
use App\Models\IncomeSource;
use Illuminate\Http\Request;

class RecurringIncomeController extends Controller
{
    public function index()
    {
        $recurringIncomes = RecurringIncome::where('user_id', auth()->id())->with('incomeSource')->latest()->paginate(10);
        $incomeSources = IncomeSource::where('user_id', auth()->id())->get();
        return view('recurring_incomes.index', compact('recurringIncomes', 'incomeSources'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'income_source_id' => 'required|exists:income_sources,id',
            'amount' => 'required|numeric|min:0',
            'frequency' => 'required|in:monthly,weekly,quarterly,annually',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
        ]);

        $validated['user_id'] = auth()->id();
        RecurringIncome::create($validated);

        return redirect()->route('recurring_incomes.index')->with('success', 'Recurring Income Profile added successfully.');
    }

    public function edit(RecurringIncome $recurringIncome)
    {
        if ($recurringIncome->user_id !== auth()->id()) {
            abort(403);
        }
        $incomeSources = IncomeSource::where('user_id', auth()->id())->get();
        return view('recurring_incomes.edit', compact('recurringIncome', 'incomeSources'));
    }

    public function update(Request $request, RecurringIncome $recurringIncome)
    {
        if ($recurringIncome->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'income_source_id' => 'required|exists:income_sources,id',
            'amount' => 'required|numeric|min:0',
            'frequency' => 'required|in:monthly,weekly,quarterly,annually',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
        ]);

        $recurringIncome->update($validated);

        return redirect()->route('recurring_incomes.index')->with('success', 'Recurring Income Profile updated successfully.');
    }

    public function destroy(RecurringIncome $recurringIncome)
    {
        if ($recurringIncome->user_id !== auth()->id()) {
            abort(403);
        }
        $recurringIncome->delete();
        return redirect()->route('recurring_incomes.index')->with('success', 'Recurring Income Profile deleted successfully.');
    }
}
