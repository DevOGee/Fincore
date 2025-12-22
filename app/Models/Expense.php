<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use Auditable, SoftDeletes;
    protected $fillable = [
        'user_id',
        'expense_category_id',
        'category',
        'amount',
        'date',
        'recipient',
        'payment_method',
        'description',
        'is_recurring',
    ];

    protected $casts = [
        'date' => 'date',
        'is_recurring' => 'boolean',
    ];

    public function expenseCategory()
    {
        return $this->belongsTo(\App\Models\ExpenseCategory::class);
    }
}
