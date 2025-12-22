<?php

namespace App\Imports;

use App\Models\Budget;
use App\Models\ExpenseCategory;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BudgetsImport implements ToCollection, WithHeadingRow
{
    /**
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        $userId = Auth::id();
        
        foreach ($rows as $row) {
            // Skip rows that don't have required fields
            if (empty($row['expense_category_id']) || empty($row['limit']) || empty($row['period']) || empty($row['flexibility'])) {
                continue;
            }

            // Prepare the data
            $data = [
                'user_id' => $userId,
                'expense_category_id' => $row['expense_category_id'],
                'limit' => $row['limit'],
                'period' => strtolower($row['period']),
                'flexibility' => strtolower($row['flexibility']),
                'start_date' => $row['start_date'] ?? null,
                'end_date' => $row['end_date'] ?? null,
            ];

            // Get category name for legacy support
            $category = ExpenseCategory::where('id', $row['expense_category_id'])
                ->where('user_id', $userId)
                ->first();
                
            $data['category'] = $category ? $category->name : 'Imported Budget';

            // Create or update the budget
            Budget::updateOrCreate(
                [
                    'user_id' => $userId,
                    'expense_category_id' => $data['expense_category_id'],
                    'period' => $data['period']
                ],
                $data
            );
        }
    }

    /**
     * Set the expected heading row format
     */
    public function headingRow(): int
    {
        return 1;
    }
}
