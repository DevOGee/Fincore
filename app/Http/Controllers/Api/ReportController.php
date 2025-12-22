<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * List available report types and their configurations.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return response()->json([
            'reports' => [
                [
                    'id' => 'monthly',
                    'name' => 'Monthly Financial Report',
                    'description' => 'Comprehensive overview of monthly financial activity',
                    'formats' => ['pdf', 'excel'],
                    'time_frames' => ['monthly', 'custom']
                ],
                [
                    'id' => 'annual',
                    'name' => 'Annual Financial Statement',
                    'description' => 'Yearly financial summary and net worth',
                    'formats' => ['pdf', 'excel'],
                    'time_frames' => ['annual', 'custom']
                ],
                [
                    'id' => 'expense',
                    'name' => 'Expense Report',
                    'description' => 'Detailed breakdown of expenses by category',
                    'formats' => ['pdf', 'excel'],
                    'time_frames' => ['monthly', 'annual', 'custom']
                ],
                [
                    'id' => 'budget',
                    'name' => 'Budget Performance Report',
                    'description' => 'Budget vs actual spending analysis',
                    'formats' => ['pdf', 'excel'],
                    'time_frames' => ['monthly', 'custom']
                ],
                [
                    'id' => 'loan',
                    'name' => 'Loan & Debt Report',
                    'description' => 'Overview of loans and repayment status',
                    'formats' => ['pdf', 'excel'],
                    'time_frames' => ['all', 'custom']
                ],
                [
                    'id' => 'net-worth',
                    'name' => 'Net Worth Report',
                    'description' => 'Assets, liabilities, and net worth over time',
                    'formats' => ['pdf', 'excel'],
                    'time_frames' => ['all', 'custom']
                ],
            ]
        ]);
    }

    /**
     * Generate a new report.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function generate(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:monthly,annual,expense,budget,loan,net-worth',
            'format' => 'required|in:pdf,excel',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'month' => 'nullable|date_format:Y-m',
            'year' => 'nullable|integer|min:2000|max:2100',
        ]);

        $user = $request->user();
        $type = $validated['type'];
        $format = $validated['format'];

        // Set date range based on input
        $startDate = null;
        $endDate = null;
        $year = $validated['year'] ?? null;
        
        if (!empty($validated['month'])) {
            $startDate = Carbon::parse($validated['month'])->startOfMonth();
            $endDate = $startDate->copy()->endOfMonth();
        } elseif (!empty($validated['start_date']) && !empty($validated['end_date'])) {
            $startDate = Carbon::parse($validated['start_date']);
            $endDate = Carbon::parse($validated['end_date']);
        } elseif (!empty($validated['start_date'])) {
            $startDate = Carbon::parse($validated['start_date']);
            $endDate = now();
        } elseif (!empty($validated['end_date'])) {
            $endDate = Carbon::parse($validated['end_date']);
            $startDate = $endDate->copy()->subMonth();
        } elseif ($type === 'annual' && $year) {
            $startDate = Carbon::create($year)->startOfYear();
            $endDate = Carbon::create($year)->endOfYear();
        } else {
            // Default to current month
            $startDate = now()->startOfMonth();
            $endDate = now()->endOfMonth();
        }

        // Initialize report service
        $reportService = new ReportService($user, $startDate, $endDate);

        // Generate the report based on type
        try {
            switch ($type) {
                case 'monthly':
                    $result = $reportService->generateMonthlyReport($format);
                    break;
                case 'annual':
                    $result = $reportService->generateAnnualReport($year, $format);
                    break;
                case 'expense':
                    $result = $reportService->generateExpenseReport($format);
                    break;
                case 'budget':
                    $result = $reportService->generateBudgetPerformanceReport($format);
                    break;
                case 'loan':
                    $result = $reportService->generateLoanReport($format);
                    break;
                case 'net-worth':
                    $result = $reportService->generateNetWorthReport($format);
                    break;
                default:
                    return response()->json(['message' => 'Invalid report type'], 400);
            }

            return response()->json([
                'message' => 'Report generated successfully',
                'download_url' => $result['url'],
                'filename' => $result['filename'],
                'type' => $result['type']
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to generate report',
                'error' => $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTraceAsString() : null
            ], 500);
        }
    }

    /**
     * List user's generated reports.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function history(Request $request)
    {
        $reports = $request->user()->reports()
            ->orderBy('generated_at', 'desc')
            ->paginate(15);

        return response()->json([
            'data' => $reports->items(),
            'pagination' => [
                'current_page' => $reports->currentPage(),
                'last_page' => $reports->lastPage(),
                'per_page' => $reports->perPage(),
                'total' => $reports->total(),
            ]
        ]);
    }

    /**
     * Download a generated report.
     *
     * @param  int  $id
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\JsonResponse
     */
    public function download($id)
    {
        $report = Report::where('user_id', auth()->id())
            ->findOrFail($id);

        if (!Storage::exists($report->file_path)) {
            return response()->json(['message' => 'Report file not found'], 404);
        }

        $headers = [
            'Content-Type' => $report->format === 'pdf' 
                ? 'application/pdf' 
                : 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . basename($report->file_path) . '"',
        ];

        return response()->file(storage_path('app/' . $report->file_path), $headers);
    }

    /**
     * Delete a generated report.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $report = Report::where('user_id', auth()->id())
            ->findOrFail($id);

        // Delete the file if it exists
        if (Storage::exists($report->file_path)) {
            Storage::delete($report->file_path);
        }

        $report->delete();

        return response()->json(['message' => 'Report deleted successfully']);
    }
}
