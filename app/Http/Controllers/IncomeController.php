<?php

namespace App\Http\Controllers;

use App\Models\Income;
use Illuminate\Http\Request;

class IncomeController extends Controller
{
    public function index(Request $request)
    {
        $query = Income::where('user_id', auth()->id());

        if ($request->has('month')) {
            $query->whereMonth('date', date('m', strtotime($request->month)))
                ->whereYear('date', date('Y', strtotime($request->month)));
        }

        if ($request->has('source_id')) {
            $query->where('income_source_id', $request->source_id);
        }

        $incomes = $query->latest()->paginate(10);

        // Aggregations
        if ($request->has('month')) {
            $targetDate = \Carbon\Carbon::parse($request->month);
            $currentMonth = $targetDate->month;
            $currentYear = $targetDate->year;
        } else {
            $targetDate = now();
            $currentMonth = $targetDate->month;
            $currentYear = $targetDate->year;
        }

        $monthlyTotal = Income::where('user_id', auth()->id())
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->sum('amount');

        $ytdTotal = Income::where('user_id', auth()->id())
            ->whereYear('date', $currentYear)
            ->whereMonth('date', '<=', $currentMonth)
            ->sum('amount');

        $annualTotal = Income::where('user_id', auth()->id())
            ->whereYear('date', $currentYear)
            ->sum('amount');

        $incomeSources = \App\Models\IncomeSource::where('user_id', auth()->id())->get();

        // Analytics
        // 1. Income Trend (Last 12 Months)
        $incomeTrend = Income::where('user_id', auth()->id())
            ->where('date', '>=', now()->subMonths(11)->startOfMonth())
            ->selectRaw('DATE_FORMAT(date, "%Y-%m") as month, SUM(amount) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('total', 'month');

        // 2. Income Distribution by Type
        $incomeDistribution = Income::where('incomes.user_id', auth()->id())
            ->whereYear('date', $currentYear)
            ->join('income_sources', 'incomes.income_source_id', '=', 'income_sources.id')
            ->selectRaw('income_sources.type as type, SUM(incomes.amount) as total')
            ->groupBy('type')
            ->pluck('total', 'type');

        // 3. Top 5 Income Sources (YTD)
        $topSources = Income::where('incomes.user_id', auth()->id())
            ->whereYear('date', $currentYear)
            ->join('income_sources', 'incomes.income_source_id', '=', 'income_sources.id')
            ->selectRaw('income_sources.name as name, SUM(incomes.amount) as total')
            ->groupBy('name')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // 4. MoM Growth
        $lastMonthDate = $targetDate->copy()->subMonth();
        $lastMonthTotal = Income::where('user_id', auth()->id())
            ->whereMonth('date', $lastMonthDate->month)
            ->whereYear('date', $lastMonthDate->year)
            ->sum('amount');

        $momGrowth = $lastMonthTotal > 0 ? (($monthlyTotal - $lastMonthTotal) / $lastMonthTotal) * 100 : 0;

        // 5. Projected Annual Income
        $monthsElapsed = $currentMonth;
        $projectedAnnual = $monthsElapsed > 0 ? ($ytdTotal / $monthsElapsed) * 12 : 0;

        return view('incomes.index', compact('incomes', 'monthlyTotal', 'ytdTotal', 'annualTotal', 'incomeSources', 'incomeTrend', 'incomeDistribution', 'topSources', 'momGrowth', 'projectedAnnual', 'currentMonth', 'currentYear'));
    }

    public function create()
    {
        $incomeSources = \App\Models\IncomeSource::where('user_id', auth()->id())->get();
        return view('incomes.create', compact('incomeSources'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'income_source_id' => 'required|exists:income_sources,id',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'category' => 'nullable|string|max:255',
            'account_credited' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $validated['user_id'] = auth()->id();
        // Fallback for source name if needed, or just rely on relation
        $source = \App\Models\IncomeSource::find($validated['income_source_id']);
        $validated['source'] = $source->name;

        Income::create($validated);

        return redirect()->route('incomes.index')->with('success', 'Income added successfully.');
    }

    public function edit(Income $income)
    {
        if ($income->user_id !== auth()->id()) {
            abort(403);
        }
        $incomeSources = \App\Models\IncomeSource::where('user_id', auth()->id())->get();
        return view('incomes.create', ['income' => $income, 'incomeSources' => $incomeSources]);
    }

    public function update(Request $request, Income $income)
    {
        if ($income->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'income_source_id' => 'required|exists:income_sources,id',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'category' => 'nullable|string|max:255',
            'account_credited' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $source = \App\Models\IncomeSource::find($validated['income_source_id']);
        $validated['source'] = $source->name;

        $income->update($validated);

        return redirect()->route('incomes.index')->with('success', 'Income updated successfully.');
    }

    public function destroy(Income $income)
    {
        if ($income->user_id !== auth()->id()) {
            abort(403);
        }
        $income->delete();
        return redirect()->route('incomes.index')->with('success', 'Income deleted successfully.');
    }
}
