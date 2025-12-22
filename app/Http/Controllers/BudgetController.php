<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\BudgetsImport;
use App\Exports\BudgetsExport;

class BudgetController extends Controller
{

    public function index()
    {
        $budgets = Budget::where('user_id', auth()->id())->latest()->paginate(10);
        return view('budgets.index', compact('budgets'));
    }

    public function create()
    {
        $categories = \App\Models\ExpenseCategory::where('user_id', auth()->id())->get();
        return view('budgets.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate(Budget::rules());
        $validated['user_id'] = auth()->id();
        $validated['status'] = $request->input('status', Budget::STATUS_PENDING);

        // If category is selected, use its name as the budget category name (legacy support)
        if (isset($validated['expense_category_id'])) {
            $category = \App\Models\ExpenseCategory::find($validated['expense_category_id']);
            $validated['category'] = $category->name;
        } else {
            $validated['category'] = 'Global Budget';
        }

        Budget::create($validated);

        return redirect()->route('budgets.index')
            ->with('success', 'Budget created successfully. ' .
                ($validated['status'] === Budget::STATUS_APPROVED ? 'Budget is now active.' : 'Waiting for approval.'));
    }

    public function edit(Budget $budget)
    {
        if ($budget->user_id !== auth()->id()) {
            abort(403);
        }
        $categories = \App\Models\ExpenseCategory::where('user_id', auth()->id())->get();
        return view('budgets.edit', compact('budget', 'categories'));
    }

    public function update(Request $request, Budget $budget)
    {
        if ($budget->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate(Budget::rules());

        // Preserve the current status if not provided or not authorized to change
        if (!$request->has('status') || !auth()->user()->can('updateStatus', $budget)) {
            $validated['status'] = $budget->status;
        }

        // If category is selected, use its name as the budget category name (legacy support)
        if (isset($validated['expense_category_id'])) {
            $category = \App\Models\ExpenseCategory::find($validated['expense_category_id']);
            $validated['category'] = $category->name;
        } else {
            $validated['category'] = 'Global Budget';
        }

        $budget->update($validated);

        $message = 'Budget updated successfully';
        if ($budget->wasChanged('status')) {
            $statusLabels = array_flip(Budget::$statuses);
            $message .= '. Status changed to: ' . ($statusLabels[$budget->status] ?? $budget->status);
        }

        return redirect()->route('budgets.index')->with('success', $message . '.');
    }

    public function destroy(Budget $budget)
    {
        if ($budget->user_id !== auth()->id()) {
            abort(403);
        }
        $budget->delete();
        return redirect()->route('budgets.index')->with('success', 'Budget deleted successfully.');
    }

    public function importForm()
    {
        return view('budgets.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        try {
            Excel::import(new BudgetsImport, $request->file('file'));
            return redirect()->route('budgets.index')->with('success', 'Budgets imported successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error importing file: ' . $e->getMessage());
        }
    }

    public function exportTemplate()
    {
        $categories = ExpenseCategory::where('user_id', auth()->id())->get();

        $data = [
            [
                'expense_category_id' => '1',
                'limit' => '10000.00',
                'period' => 'monthly',
                'flexibility' => 'soft',
                'start_date' => now()->format('Y-m-d'),
                'end_date' => now()->addMonth()->format('Y-m-d'),
                'note' => 'Example: Food budget',
            ],
            [
                'expense_category_id' => '2',
                'limit' => '5000.00',
                'period' => 'monthly',
                'flexibility' => 'strict',
                'start_date' => now()->format('Y-m-d'),
                'end_date' => now()->addYear()->format('Y-m-d'),
                'note' => 'Example: Transportation budget',
            ]
        ];

        // Add category names for reference
        $categoryReference = [];
        foreach ($categories as $category) {
            $categoryReference[] = [
                'ID: ' . $category->id => $category->name . ' (' . $category->type . ')'
            ];
        }

        $export = new BudgetsExport(collect($data), $categoryReference);
        return Excel::download($export, 'budget_template.xlsx');
    }
    public function approve(Budget $budget)
    {
        if ($budget->user_id !== auth()->id()) {
            abort(403);
        }

        if ($budget->status === 'approved') {
            return back()->with('error', 'Budget is already approved.');
        }

        $budget->update(['status' => 'approved']);

        return back()->with('success', 'Budget approved successfully.');
    }

    public function disapprove(Budget $budget)
    {
        if ($budget->user_id !== auth()->id()) {
            abort(403);
        }

        if ($budget->status === 'rejected') {
            return back()->with('error', 'Budget is already rejected.');
        }

        $budget->update(['status' => 'rejected']);

        return back()->with('success', 'Budget has been rejected.');
    }
}
