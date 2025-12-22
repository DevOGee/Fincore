<?php

namespace App\Http\Controllers;

use App\Models\SavingsGoal;
use Illuminate\Http\Request;

class SavingsGoalController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        $savingsGoals = SavingsGoal::where('user_id', $userId)->latest()->get(); // Get all for summary, can paginate later if needed or handle pagination + summary separately

        $totalTarget = $savingsGoals->sum('target_amount');
        $totalSaved = $savingsGoals->sum('current_amount');
        $overallProgress = $totalTarget > 0 ? ($totalSaved / $totalTarget) * 100 : 0;

        // For the list, we might still want pagination if there are many, 
        // but for now let's keep it simple or re-query for pagination if list is long.
        // Let's stick to pagination for the list to be safe, but use the full collection for totals.
        // Optimization: In a real app, use aggregate queries for totals.
        $totalTarget = SavingsGoal::where('user_id', $userId)->sum('target_amount');
        $totalSaved = SavingsGoal::where('user_id', $userId)->sum('current_amount');
        $overallProgress = $totalTarget > 0 ? ($totalSaved / $totalTarget) * 100 : 0;

        $savingsGoals = SavingsGoal::where('user_id', $userId)->latest()->paginate(9); // 9 for grid layout (3x3)

        return view('savings_goals.index', compact('savingsGoals', 'totalTarget', 'totalSaved', 'overallProgress'));
    }

    public function create()
    {
        return view('savings_goals.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'target_amount' => 'required|numeric|min:0',
            'current_amount' => 'required|numeric|min:0',
            'deadline' => 'nullable|date',
            'description' => 'nullable|string',
            'monthly_contribution' => 'nullable|numeric|min:0',
            'funding_source' => 'required|string|in:income_percentage,surplus,legacy',
            'start_date' => 'nullable|date',
        ]);

        $validated['user_id'] = auth()->id();
        SavingsGoal::create($validated);

        return redirect()->route('savings_goals.index')->with('success', 'Savings Goal created successfully.');
    }

    public function show(SavingsGoal $savingsGoal)
    {
        if ($savingsGoal->user_id !== auth()->id()) {
            abort(403);
        }

        return view('savings_goals.show', compact('savingsGoal'));
    }

    public function destroy(SavingsGoal $savingsGoal)
    {
        if ($savingsGoal->user_id !== auth()->id()) {
            abort(403);
        }
        $savingsGoal->delete();
        return redirect()->route('savings_goals.index')->with('success', 'Savings Goal deleted successfully.');
    }
}
