<?php

namespace App\Http\Controllers;

use App\Models\SavingsGoal;
use Illuminate\Http\Request;

class SavingsGoalController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        // Fetch all goals for logic
        $allGoals = SavingsGoal::where('user_id', $userId)->get();

        // Summary Calculations
        $totalTarget = $allGoals->sum('target_amount');
        $totalSaved = $allGoals->sum('current_amount');
        $overallProgress = $totalTarget > 0 ? ($totalSaved / $totalTarget) * 100 : 0;

        // --- Goal Distribution Data ---
        $goalDistribution = [
            'labels' => $allGoals->pluck('name')->toArray(),
            'series' => $allGoals->pluck('current_amount')->toArray(),
        ];

        // --- Savings Growth Projection Data ---
        // Projection for next 5 years
        $monthsToProject = 60;
        $growthData = [
            'monthly' => ['labels' => [], 'data' => []],
            'quarterly' => ['labels' => [], 'data' => []],
            'yearly' => ['labels' => [], 'data' => []],
        ];

        // Base accumulated amount starts with current total saved
        $currentProjectionAmount = $totalSaved;
        $monthlyTotalContribution = $allGoals->sum('monthly_contribution');

        $currentDate = now();

        for ($i = 0; $i <= $monthsToProject; $i++) {
            $date = $currentDate->copy()->addMonths($i);

            // Add monthly contribution for this month (simple projection)
            // In a real scenario, we would check if a goal's deadline has passed and stop adding its contribution
            // For now, we assume contributions continue until the projection ends or we could become more sophisticated
            $activeContribution = 0;
            foreach ($allGoals as $goal) {
                // If goal has a deadline and we passed it, don't add contribution? 
                // Or assume user re-allocates that money? Let's assume re-allocation (simple growth) 
                // OR simpler: check if goal deadline is active.
                if (!$goal->deadline || $date->lt($goal->deadline)) {
                    $activeContribution += $goal->monthly_contribution;
                }
            }

            // Verify if it's the start (0th month) - just show current amount
            if ($i > 0) {
                $currentProjectionAmount += $activeContribution;
            }

            // Monthly Data
            $growthData['monthly']['labels'][] = $date->format('M Y');
            $growthData['monthly']['data'][] = round($currentProjectionAmount);

            // Quarterly Data (Jan, Apr, Jul, Oct)
            if (in_array($date->month, [1, 4, 7, 10])) {
                $growthData['quarterly']['labels'][] = $date->format('M Y');
                $growthData['quarterly']['data'][] = round($currentProjectionAmount);
            }

            // Yearly Data (Jan)
            if ($date->month === 1) {
                $growthData['yearly']['labels'][] = $date->format('Y');
                $growthData['yearly']['data'][] = round($currentProjectionAmount);
            }
        }

        // Paginate for the grid view
        $savingsGoals = SavingsGoal::where('user_id', $userId)->latest()->paginate(9);

        return view('savings_goals.index', compact(
            'savingsGoals',
            'totalTarget',
            'totalSaved',
            'overallProgress',
            'goalDistribution',
            'growthData'
        ));
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

    public function edit(SavingsGoal $savingsGoal)
    {
        if ($savingsGoal->user_id !== auth()->id()) {
            abort(403);
        }
        return view('savings_goals.edit', compact('savingsGoal'));
    }

    public function update(Request $request, SavingsGoal $savingsGoal)
    {
        if ($savingsGoal->user_id !== auth()->id()) {
            abort(403);
        }

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

        $savingsGoal->update($validated);

        return redirect()->route('savings_goals.index')->with('success', 'Savings Goal updated successfully.');
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
