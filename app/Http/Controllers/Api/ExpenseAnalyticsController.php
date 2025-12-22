<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Budget;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpenseAnalyticsController extends Controller
{
    public function monthlySummary(Request $request)
    {
        $month = $request->input('month', now()->format('Y-m'));
        $startDate = Carbon::parse($month)->startOfMonth();
        $endDate = Carbon::parse($month)->endOfMonth();

        $totalExpenses = Expense::where('user_id', auth()->id())
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('amount');

        $totalBudget = Budget::where('user_id', auth()->id())
            ->where('starts_at', '<=', $endDate)
            ->where('ends_at', '>=', $startDate)
            ->sum('amount');

        return response()->json([
            'total_budget' => (float) $totalBudget,
            'total_expenses' => (float) $totalExpenses,
            'remaining_budget' => $totalBudget - $totalExpenses,
            'over_budget' => $totalExpenses > $totalBudget
        ]);
    }

    public function dailyTrend(Request $request)
    {
        $month = $request->input('month', now()->format('Y-m'));
        $startDate = Carbon::parse($month)->startOfMonth();
        $endDate = Carbon::parse($month)->endOfMonth();
        $daysInMonth = $endDate->day;

        $expenses = Expense::where('user_id', auth()->id())
            ->whereBetween('date', [$startDate, $endDate])
            ->select(
                DB::raw('DAY(date) as day'),
                DB::raw('SUM(amount) as amount')
            )
            ->groupBy('day')
            ->pluck('amount', 'day')
            ->toArray();

        $dailyData = [];
        $cumulativeData = [];
        $cumulative = 0;

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $amount = $expenses[$day] ?? 0;
            $cumulative += $amount;
            $dailyData[] = $amount;
            $cumulativeData[] = $cumulative;
        }

        $totalBudget = Budget::where('user_id', auth()->id())
            ->where('starts_at', '<=', $endDate)
            ->where('ends_at', '>=', $startDate)
            ->sum('amount');

        return response()->json([
            'labels' => range(1, $daysInMonth),
            'expenses' => $dailyData,
            'cumulative' => $cumulativeData,
            'budget_line' => array_fill(0, $daysInMonth, $totalBudget / $daysInMonth * 1.0) // Daily budget
        ]);
    }

    public function byCategory(Request $request)
    {
        $month = $request->input('month', now()->format('Y-m'));
        $startDate = Carbon::parse($month)->startOfMonth();
        $endDate = Carbon::parse($month)->endOfMonth();

        $expenses = DB::table('expenses')
            ->join('expense_categories', 'expenses.expense_category_id', '=', 'expense_categories.id')
            ->where('expenses.user_id', auth()->id())
            ->whereBetween('expenses.date', [$startDate, $endDate])
            ->select(
                'expense_categories.name as category',
                DB::raw('SUM(expenses.amount) as spent')
            )
            ->groupBy('expense_categories.name')
            ->get();

        $budgets = DB::table('budgets')
            ->join('expense_categories', 'budgets.expense_category_id', '=', 'expense_categories.id')
            ->where('budgets.user_id', auth()->id())
            ->where('budgets.starts_at', '<=', $endDate)
            ->where('budgets.ends_at', '>=', $startDate)
            ->select(
                'expense_categories.name as category',
                'budgets.amount as budget'
            )
            ->get()
            ->keyBy('category');

        $categories = $expenses->pluck('category')->merge($budgets->pluck('category'))->unique();

        $result = $categories->map(function ($category) use ($expenses, $budgets) {
            $expense = $expenses->where('category', $category)->first();
            $budget = $budgets->where('category', $category)->first();

            return [
                'category' => $category,
                'spent' => $expense ? (float) $expense->spent : 0,
                'budget' => $budget ? (float) $budget->budget : 0
            ];
        });

        return response()->json([
            'labels' => $result->pluck('category'),
            'spent' => $result->pluck('spent'),
            'budget' => $result->pluck('budget')
        ]);
    }
}
