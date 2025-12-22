<?php

namespace App\Http\Controllers;

use App\Models\SavingsAllocationRule;
use App\Models\SavingsLegacy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SavingsLegacyController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // KPI: Total Legacy Balance
        $totalLegacy = SavingsLegacy::where('user_id', $user->id)->sum('amount');

        // KPI: This Month's Contribution
        $thisMonthContribution = SavingsLegacy::where('user_id', $user->id)
            ->where('month', now()->format('Y-m'))
            ->sum('amount');

        // KPI: Quarterly Growth (Last Quarter vs Current Quarter)
        $currentQuarter = 'Q' . now()->quarter;
        $lastQuarter = 'Q' . (now()->subQuarter()->quarter);

        $currentQuarterTotal = SavingsLegacy::where('user_id', $user->id)
            ->where('year', now()->year)
            ->where('quarter', $currentQuarter)
            ->sum('amount');

        $lastQuarterTotal = SavingsLegacy::where('user_id', $user->id)
            ->where('year', now()->subQuarter()->year)
            ->where('quarter', $lastQuarter)
            ->sum('amount');

        $quarterlyGrowth = $lastQuarterTotal > 0
            ? (($currentQuarterTotal - $lastQuarterTotal) / $lastQuarterTotal) * 100
            : ($currentQuarterTotal > 0 ? 100 : 0);

        // Chart 1: Allocation Breakdown
        $allocationData = SavingsLegacy::where('user_id', $user->id)
            ->select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->get();

        $allocationLabels = $allocationData->pluck('category')->toArray();
        $allocationSeries = $allocationData->pluck('total')->toArray();

        // Chart 2: Growth Over Time (Last 12 Months)
        $growthData = SavingsLegacy::where('user_id', $user->id)
            ->select('month', DB::raw('SUM(amount) as total'))
            ->groupBy('month')
            ->orderBy('month')
            ->limit(12)
            ->get();

        $growthLabels = $growthData->pluck('month')->toArray();
        $growthLabels = $growthData->pluck('month')->toArray();
        $growthSeries = $growthData->pluck('total')->toArray();

        $entries = SavingsLegacy::where('user_id', $user->id)->latest('date')->paginate(10);

        return view('savings.legacy.index', compact(
            'totalLegacy',
            'thisMonthContribution',
            'quarterlyGrowth',
            'allocationLabels',
            'allocationSeries',
            'growthLabels',
            'growthLabels',
            'growthSeries',
            'entries'
        ));
    }

    public function settings()
    {
        $rules = SavingsAllocationRule::where('user_id', auth()->id())->get();
        return view('savings.legacy.settings', compact('rules'));
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'rules' => 'required|array',
            'rules.*.category' => 'required|string',
            'rules.*.percentage' => 'required|numeric|min:0|max:100',
            'rules.*.is_active' => 'boolean',
        ]);

        foreach ($request->rules as $ruleData) {
            SavingsAllocationRule::updateOrCreate(
                [
                    'user_id' => auth()->id(),
                    'category' => $ruleData['category']
                ],
                [
                    'percentage' => $ruleData['percentage'],
                    'is_active' => $ruleData['is_active'] ?? true
                ]
            );
        }

        return redirect()->route('savings.legacy.index')->with('success', 'Allocation rules updated.');
    }
    public function create()
    {
        return view('savings.legacy.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'percentage_applied' => 'nullable|numeric|min:0|max:100',
        ]);

        $date = \Carbon\Carbon::parse($validated['date']);

        SavingsLegacy::create([
            'user_id' => auth()->id(),
            'category' => $validated['category'],
            'amount' => $validated['amount'],
            'percentage_applied' => $validated['percentage_applied'] ?? 0,
            'date' => $validated['date'],
            'month' => $date->format('Y-m'),
            'year' => $date->year,
            'quarter' => 'Q' . $date->quarter,
        ]);

        return redirect()->route('savings.legacy.index')->with('success', 'Legacy savings entry added successfully.');
    }

    public function edit(SavingsLegacy $savingsLegacy)
    {
        if ($savingsLegacy->user_id !== auth()->id()) {
            abort(403);
        }
        return view('savings.legacy.edit', compact('savingsLegacy'));
    }

    public function update(Request $request, SavingsLegacy $savingsLegacy)
    {
        if ($savingsLegacy->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'category' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'percentage_applied' => 'nullable|numeric|min:0|max:100',
        ]);

        $date = \Carbon\Carbon::parse($validated['date']);

        $savingsLegacy->update([
            'category' => $validated['category'],
            'amount' => $validated['amount'],
            'percentage_applied' => $validated['percentage_applied'] ?? 0,
            'date' => $validated['date'],
            'month' => $date->format('Y-m'),
            'year' => $date->year,
            'quarter' => 'Q' . $date->quarter,
        ]);

        return redirect()->route('savings.legacy.index')->with('success', 'Legacy savings entry updated successfully.');
    }

    public function destroy(SavingsLegacy $savingsLegacy)
    {
        if ($savingsLegacy->user_id !== auth()->id()) {
            abort(403);
        }
        $savingsLegacy->delete();
        return redirect()->route('savings.legacy.index')->with('success', 'Legacy savings entry deleted successfully.');
    }
}
