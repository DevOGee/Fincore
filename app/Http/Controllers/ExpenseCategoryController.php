<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExpenseCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = \App\Models\ExpenseCategory::where('user_id', auth()->id())->get();
        return view('expense_categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:fixed,variable',
            'is_essential' => 'boolean',
            'default_budget_cap' => 'nullable|numeric|min:0',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['is_essential'] = $request->has('is_essential');

        \App\Models\ExpenseCategory::create($validated);

        return redirect()->route('expense_categories.index')->with('success', 'Category created successfully.');
    }

    public function edit(\App\Models\ExpenseCategory $expenseCategory)
    {
        if ($expenseCategory->user_id !== auth()->id()) {
            abort(403);
        }
        return view('expense_categories.edit', compact('expenseCategory'));
    }

    public function update(Request $request, \App\Models\ExpenseCategory $expenseCategory)
    {
        if ($expenseCategory->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:fixed,variable',
            'is_essential' => 'boolean',
            'default_budget_cap' => 'nullable|numeric|min:0',
        ]);

        $validated['is_essential'] = $request->has('is_essential');

        $expenseCategory->update($validated);

        return redirect()->route('expense_categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy(\App\Models\ExpenseCategory $expenseCategory)
    {
        if ($expenseCategory->user_id !== auth()->id()) {
            abort(403);
        }

        $expenseCategory->delete();

        return redirect()->route('expense_categories.index')->with('success', 'Category deleted successfully.');
    }
}
