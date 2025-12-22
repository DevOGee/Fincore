<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChartController extends Controller
{
    public function expensesByCategory()
    {
        $expenses = Expense::where('user_id', auth()->id())
            ->select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->get();

        return response()->json([
            'labels' => $expenses->pluck('category'),
            'data' => $expenses->pluck('total'),
        ]);
    }

    public function monthlyExpenses()
    {
        $expenses = Expense::where('user_id', auth()->id())
            ->select(
                DB::raw('DATE_FORMAT(date, "%Y-%m") as month'),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->limit(12)
            ->get();

        return response()->json([
            'labels' => $expenses->pluck('month'),
            'data' => $expenses->pluck('total'),
        ]);
    }

    public function monthlyIncome()
    {
        $incomes = \App\Models\Income::where('user_id', auth()->id())
            ->select(
                DB::raw('DATE_FORMAT(date, "%Y-%m") as month'),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->limit(12)
            ->get();

        return response()->json([
            'labels' => $incomes->pluck('month'),
            'data' => $incomes->pluck('total'),
        ]);
    }

    public function cashFlow()
    {
        // Simple cash flow: Income - Expenses per month
        $incomes = \App\Models\Income::where('user_id', auth()->id())
            ->select(
                DB::raw('DATE_FORMAT(date, "%Y-%m") as month'),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $expenses = Expense::where('user_id', auth()->id())
            ->select(
                DB::raw('DATE_FORMAT(date, "%Y-%m") as month'),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        // Merge keys
        $resLabels = $incomes->keys()->merge($expenses->keys())->unique()->sort()->values();
        $resData = [];

        foreach ($resLabels as $month) {
            $inc = $incomes[$month]->total ?? 0;
            $exp = $expenses[$month]->total ?? 0;
            $resData[] = $inc - $exp;
        }

        return response()->json([
            'labels' => $resLabels,
            'data' => $resData,
        ]);
    }
}
