<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Events\ExpenseUpdated;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = Expense::where('user_id', auth()->id())->latest()->paginate(10);
        return view('expenses.index', compact('expenses'));
    }

    public function create()
    {
        $categories = \App\Models\ExpenseCategory::where('user_id', auth()->id())->get();
        return view('expenses.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'recipient' => 'nullable|string|max:255',
            'payment_method' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_recurring' => 'boolean',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['is_recurring'] = $request->has('is_recurring');

        $category = \App\Models\ExpenseCategory::find($validated['expense_category_id']);
        $validated['category'] = $category->name;

        // Budget Enforcement Logic
        $amount = $validated['amount'];
        $date = \Carbon\Carbon::parse($validated['date']);

        // Find active budget for this category and month
        $budget = \App\Models\Budget::where('user_id', auth()->id())
            ->where(function ($query) use ($validated) {
                $query->where('expense_category_id', $validated['expense_category_id'])
                    ->orWhereNull('expense_category_id'); // Fallback to global budget
            })
            ->where('period', 'monthly') // Assuming monthly for now
            ->orderByRaw('expense_category_id IS NULL ASC') // Prioritize non-null (specific) budgets
            ->first();

        if ($budget) {
            // Calculate total spend for this category/month
            $currentSpend = Expense::where('user_id', auth()->id())
                ->where('expense_category_id', $validated['expense_category_id'])
                ->whereMonth('date', $date->month)
                ->whereYear('date', $date->year)
                ->sum('amount');

            $remaining = $budget->limit - $currentSpend;

            if ($amount > $remaining) {
                $excess = $amount - $remaining;

                if ($budget->flexibility === 'strict') {
                    return back()->withErrors(['amount' => "Budget limit exceeded! Remaining: KES {$remaining}. Transaction blocked (Strict Budget)."])->withInput();
                } else {
                    // Soft budget: Create Debt record
                    $expense = Expense::create($validated);

                    \App\Models\Debt::create([
                        'user_id' => auth()->id(),
                        'source_expense_id' => $expense->id,
                        'amount' => $excess,
                        'description' => "Over-budget: {$category->name} (Limit: {$budget->limit})",
                        'status' => 'pending',
                    ]);

                    return redirect()->route('expenses.index')->with('warning', "Budget exceeded! KES {$excess} recorded as debt.");
                }
            }
        }

        $expense = Expense::create($validated);
        
        // Broadcast the created event
        broadcast(new ExpenseUpdated($expense, 'created'))->toOthers();

        return redirect()->route('expenses.index')->with('success', 'Expense added successfully.');
    }

    public function edit($id)
    {
        $expense = Expense::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();
            
        $categories = \App\Models\ExpenseCategory::where('user_id', auth()->id())->get();
        return view('expenses.edit', compact('expense', 'categories'));
    }

    public function update(Request $request, Expense $expense)
    {
        if ($expense->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'recipient' => 'nullable|string|max:255',
            'payment_method' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_recurring' => 'boolean',
        ]);

        $validated['is_recurring'] = $request->has('is_recurring');

        $category = \App\Models\ExpenseCategory::find($validated['expense_category_id']);
        $validated['category'] = $category->name;

        // Budget Enforcement Logic (Simplified for Update - mainly checking if amount increased significantly?)
        // For now, we'll just update. In a real app, we might want to re-check budget if amount increases.
        // Let's implement a basic check if amount increases.

        if ($validated['amount'] > $expense->amount) {
            $amountDiff = $validated['amount'] - $expense->amount;
            $date = \Carbon\Carbon::parse($validated['date']);

            $budget = \App\Models\Budget::where('user_id', auth()->id())
                ->where(function ($query) use ($validated) {
                    $query->where('expense_category_id', $validated['expense_category_id'])
                        ->orWhereNull('expense_category_id');
                })
                ->where('period', 'monthly')
                ->orderByRaw('expense_category_id IS NULL ASC')
                ->first();

            if ($budget) {
                $currentSpend = Expense::where('user_id', auth()->id())
                    ->where('expense_category_id', $validated['expense_category_id'])
                    ->whereMonth('date', $date->month)
                    ->whereYear('date', $date->year)
                    ->where('id', '!=', $expense->id) // Exclude current expense
                    ->sum('amount');

                $remaining = $budget->limit - $currentSpend;

                if ($validated['amount'] > $remaining) {
                    $excess = $validated['amount'] - $remaining;
                    if ($budget->flexibility === 'strict') {
                        return back()->withErrors(['amount' => "Budget limit exceeded! Remaining: KES {$remaining}. Update blocked."])->withInput();
                    } else {
                        // Soft budget: Create Debt? Or just warn?
                        // If we are updating, maybe we should just warn for now or create debt for the difference.
                        // Let's just warn for simplicity in update, or create debt if it didn't exist?
                        // Complex logic. Let's stick to allowing it with a warning for soft budget.
                        session()->flash('warning', "Budget exceeded! KES {$excess} over limit.");
                    }
                }
            }
        }

        $expense->update($validated);
        
        // Broadcast the updated event
        broadcast(new ExpenseUpdated($expense->fresh(), 'updated'))->toOthers();

        return redirect()->route('expenses.index')->with('success', 'Expense updated successfully.');
    }

    public function destroy(Expense $expense)
    {
        if ($expense->user_id !== auth()->id()) {
            abort(403);
        }
        $expenseId = $expense->id;
        $expense->delete();
        
        // Create a temporary expense object for broadcasting
        $tempExpense = new Expense($expense->toArray());
        $tempExpense->id = $expenseId;
        
        // Broadcast the deleted event
        broadcast(new ExpenseUpdated($tempExpense, 'deleted'))->toOthers();
        
        return redirect()->route('expenses.index')->with('success', 'Expense deleted successfully.');
    }
}
