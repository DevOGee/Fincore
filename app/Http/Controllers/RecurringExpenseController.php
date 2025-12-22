<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RecurringExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $recurringExpenses = \App\Models\RecurringExpense::where('user_id', auth()->id())
            ->with('expenseCategory')
            ->latest()
            ->paginate(10);
        $categories = \App\Models\ExpenseCategory::where('user_id', auth()->id())->get();
        return view('recurring_expenses.index', compact('recurringExpenses', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'amount' => 'required|numeric|min:0',
            'frequency' => 'required|in:weekly,monthly,quarterly,annually',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'recipient' => 'nullable|string|max:255',
            'payment_method' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['next_run_date'] = $validated['start_date'];
        $validated['active'] = true;

        \App\Models\RecurringExpense::create($validated);

        return redirect()->route('recurring_expenses.index')->with('success', 'Recurring expense created successfully.');
    }

    public function destroy(\App\Models\RecurringExpense $recurringExpense)
    {
        if ($recurringExpense->user_id !== auth()->id()) {
            abort(403);
        }

        $recurringExpense->delete();

        return redirect()->route('recurring_expenses.index')->with('success', 'Recurring expense deleted successfully.');
    }
}
