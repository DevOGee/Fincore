<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecurringExpense extends Model
{
    protected $fillable = [
        'user_id',
        'expense_category_id',
        'amount',
        'frequency',
        'start_date',
        'end_date',
        'next_run_date',
        'active',
        'recipient',
        'payment_method',
        'description',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'next_run_date' => 'date',
        'active' => 'boolean',
    ];

    public function expenseCategory()
    {
        return $this->belongsTo(\App\Models\ExpenseCategory::class);
    }
}
