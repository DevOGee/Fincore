<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\LoanRepayment;
use App\Services\LoanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LoanController extends Controller
{
    protected $loanService;

    public function __construct(LoanService $loanService)
    {
        $this->middleware('auth:sanctum');
        $this->loanService = $loanService;
    }

    /**
     * Get all loans for the authenticated user
     */
    public function index(Request $request)
    {
        $status = $request->query('status');
        $categoryId = $request->query('category_id');
        $perPage = $request->query('per_page', 15);

        $query = Loan::forUser($request->user()->id)
            ->with(['expenseCategory'])
            ->orderBy('created_at', 'desc');

        if ($status && in_array($status, ['active', 'cleared', 'overdue'])) {
            $query->where('status', $status);
        }

        if ($categoryId) {
            $query->where('expense_category_id', $categoryId);
        }

        $loans = $query->paginate($perPage);

        return response()->json($loans);
    }

    /**
     * Get a single loan with its repayments
     */
    public function show(Loan $loan)
    {
        $this->authorize('view', $loan);

        $loan->load(['expenseCategory', 'expense', 'repayments' => function ($query) {
            $query->orderBy('payment_date', 'desc');
        }]);

        return response()->json($loan);
    }

    /**
     * Create a new manual loan
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'expense_category_id' => 'required|exists:expense_categories,id',
            'original_amount' => 'required|numeric|min:0.01',
            'outstanding_balance' => 'required|numeric|min:0|lte:original_amount',
            'interest_rate' => 'required|numeric|min:0|max:100',
            'date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:date',
            'description' => 'nullable|string',
            'reference_number' => 'nullable|string|max:100',
        ]);

        $loan = Loan::create([
            'user_id' => $request->user()->id,
            'name' => $validated['name'],
            'source' => 'manual',
            'expense_category_id' => $validated['expense_category_id'],
            'original_amount' => $validated['original_amount'],
            'outstanding_balance' => $validated['outstanding_balance'],
            'interest_rate' => $validated['interest_rate'],
            'date' => $validated['date'],
            'due_date' => $validated['due_date'],
            'description' => $validated['description'] ?? null,
            'reference_number' => $validated['reference_number'] ?? null,
            'status' => $validated['outstanding_balance'] > 0 ? 'active' : 'cleared',
            'is_auto_created' => false,
        ]);

        return response()->json($loan, 201);
    }

    /**
     * Update a loan
     */
    public function update(Request $request, Loan $loan)
    {
        $this->authorize('update', $loan);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'expense_category_id' => 'sometimes|required|exists:expense_categories,id',
            'original_amount' => 'sometimes|required|numeric|min:0.01',
            'outstanding_balance' => 'sometimes|required|numeric|min:0',
            'interest_rate' => 'sometimes|required|numeric|min:0|max:100',
            'date' => 'sometimes|required|date',
            'due_date' => 'sometimes|required|date|after_or_equal:date',
            'description' => 'nullable|string',
            'reference_number' => 'nullable|string|max:100',
            'status' => 'sometimes|required|in:active,cleared,overdue',
        ]);

        // If status is being updated to cleared, ensure outstanding_balance is 0
        if (isset($validated['status']) && $validated['status'] === 'cleared') {
            $validated['outstanding_balance'] = 0;
            $validated['cleared_at'] = now();
        }

        $loan->update($validated);

        return response()->json($loan);
    }

    /**
     * Delete a loan
     */
    public function destroy(Loan $loan)
    {
        $this->authorize('delete', $loan);

        // Only allow deletion of manual loans
        if ($loan->is_auto_created) {
            return response()->json(
                ['message' => 'Automatically created loans cannot be deleted'],
                403
            );
        }

        $loan->delete();

        return response()->json(null, 204);
    }

    /**
     * Record a loan repayment
     */
    public function recordRepayment(Request $request, Loan $loan)
    {
        $this->authorize('update', $loan);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $loan->outstanding_balance,
            'account_id' => 'nullable|exists:accounts,id',
            'payment_date' => 'required|date|before_or_equal:today',
            'notes' => 'nullable|string',
            'reference_number' => 'nullable|string|max:100',
        ]);

        $repayment = $this->loanService->recordRepayment(
            $loan,
            $validated['amount'],
            $validated['account_id'] ?? null,
            $validated['payment_date'],
            $validated['notes'] ?? null,
            $validated['reference_number'] ?? null
        );

        return response()->json($repayment, 201);
    }

    /**
     * Get loan dashboard data
     */
    public function dashboard(Request $request)
    {
        $month = $request->query('month', now()->month);
        $year = $request->query('year', now()->year);

        $data = $this->loanService->getDashboardSummary(
            $request->user()->id,
            $month,
            $year
        );

        return response()->json($data);
    }

    /**
     * Get loan statistics
     */
    public function statistics()
    {
        $stats = $this->loanService->getLoanStatistics(auth()->id());
        return response()->json($stats);
    }

    /**
     * Get debt composition by category
     */
    public function debtComposition()
    {
        $userId = auth()->id();
        
        $composition = Loan::forUser($userId)
            ->active()
            ->with('expenseCategory')
            ->get()
            ->groupBy('expense_category_id')
            ->map(function($loans, $categoryId) {
                $first = $loans->first();
                return [
                    'category_id' => $categoryId,
                    'category_name' => $first->expenseCategory->name ?? 'Uncategorized',
                    'amount' => $loans->sum('outstanding_balance'),
                    'count' => $loans->count(),
                ];
            })
            ->sortByDesc('amount')
            ->values();
            
        return response()->json($composition);
    }

    /**
     * Get debt trend over time
     */
    public function debtTrend()
    {
        $userId = auth()->id();
        $months = $request->query('months', 6);
        $endDate = now();
        $startDate = now()->subMonths($months - 1)->startOfMonth();
        
        $trend = collect();
        $currentDate = $startDate->copy();
        
        while ($currentDate <= $endDate) {
            $monthStart = $currentDate->copy()->startOfMonth();
            $monthEnd = $currentDate->copy()->endOfMonth();
            
            // Get opening balance (loans created before this month)
            $opening = Loan::forUser($userId)
                ->where('date', '<', $monthStart)
                ->where(function($q) use ($monthStart) {
                    $q->where('status', '!=', 'cleared')
                      ->orWhere('cleared_at', '>=', $monthStart);
                })
                ->sum('outstanding_balance');
                
            // Get new loans this month
            $newLoans = Loan::forUser($userId)
                ->whereBetween('date', [$monthStart, $monthEnd])
                ->sum('original_amount');
                
            // Get repayments this month
            $repayments = LoanRepayment::whereHas('loan', function($q) use ($userId) {
                    $q->where('user_id', $userId);
                })
                ->whereBetween('payment_date', [$monthStart, $monthEnd])
                ->sum('amount');
                
            $trend->push([
                'month' => $currentDate->format('M Y'),
                'opening' => $opening,
                'new_loans' => $newLoans,
                'repayments' => $repayments,
                'closing' => $opening + $newLoans - $repayments,
            ]);
            
            $currentDate->addMonth();
        }
        
        return response()->json($trend);
    }
}
