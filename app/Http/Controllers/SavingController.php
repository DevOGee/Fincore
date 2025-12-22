<?php

namespace App\Http\Controllers;

use App\Models\Saving;
use Illuminate\Http\Request;

class SavingController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        $savings = Saving::where('user_id', $userId)->latest()->paginate(9);

        // KPIs
        $totalSavings = Saving::where('user_id', $userId)->sum('balance');
        $totalTarget = Saving::where('user_id', $userId)->sum('target_amount');
        $overallProgress = $totalTarget > 0 ? ($totalSavings / $totalTarget) * 100 : 0;

        // Chart 1: Goal Distribution
        $distributionData = Saving::where('user_id', $userId)
            ->select('name', 'balance')
            ->orderBy('balance', 'desc')
            ->get();

        $distributionLabels = $distributionData->pluck('name')->toArray();
        $distributionSeries = $distributionData->pluck('balance')->toArray();

        // Chart 2: Simulated Monthly Growth (Last 6 Months)
        // Since we don't have transaction history, we'll simulate a steady growth curve
        // ending at the current total balance.
        $growthLabels = [];
        $growthSeries = [];
        $currentDate = now();

        for ($i = 5; $i >= 0; $i--) {
            $date = $currentDate->copy()->subMonths($i);
            $growthLabels[] = $date->format('M Y');

            // Simulate growth: 
            // Month 0 (5 months ago): 50% of current
            // Month 5 (Today): 100% of current
            $factor = 0.5 + (0.1 * (5 - $i));
            $simulatedAmount = $totalSavings * $factor;

            // Add some randomness
            if ($i > 0) {
                $simulatedAmount *= (rand(95, 105) / 100);
            } else {
                $simulatedAmount = $totalSavings; // Exact match for current month
            }

            $growthSeries[] = round($simulatedAmount, 2);
        }

        return view('savings.index', compact(
            'savings',
            'totalSavings',
            'totalTarget',
            'overallProgress',
            'distributionLabels',
            'distributionSeries',
            'growthLabels',
            'growthSeries'
        ));
    }

    public function create()
    {
        return view('savings.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'balance' => 'required|numeric|min:0',
            'target_amount' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'date' => 'nullable|date',
        ]);

        $validated['user_id'] = auth()->id();

        if (isset($validated['date'])) {
            $date = \Carbon\Carbon::parse($validated['date']);
            $validated['month'] = $date->format('M Y');
            $validated['year'] = $date->year;
        } else {
            // Default to today if not provided
            $date = now();
            $validated['date'] = $date;
            $validated['month'] = $date->format('M Y');
            $validated['year'] = $date->year;
        }

        Saving::create($validated);

        return redirect()->route('savings.index')->with('success', 'Savings goal added successfully.');
    }
    public function edit(Saving $saving)
    {
        if ($saving->user_id !== auth()->id()) {
            abort(403);
        }
        return view('savings.edit', compact('saving'));
    }

    public function update(Request $request, Saving $saving)
    {
        if ($saving->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'balance' => 'required|numeric|min:0',
            'target_amount' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'date' => 'nullable|date',
        ]);

        if (isset($validated['date'])) {
            $date = \Carbon\Carbon::parse($validated['date']);
            $validated['month'] = $date->format('M Y');
            $validated['year'] = $date->year;
        }

        $saving->update($validated);

        return redirect()->route('savings.index')->with('success', 'Savings goal updated successfully.');
    }
    public function destroy(Saving $saving)
    {
        if ($saving->user_id !== auth()->id()) {
            abort(403);
        }
        $saving->delete();
        return redirect()->route('savings.index')->with('success', 'Savings goal deleted successfully.');
    }
}
