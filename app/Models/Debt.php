<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Debt extends Model
{
    protected $fillable = [
        'user_id',
        'source_expense_id',
        'amount',
        'description',
        'status',
    ];

    public function sourceExpense()
    {
        return $this->belongsTo(\App\Models\Expense::class, 'source_expense_id');
    }
}
