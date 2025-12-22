<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecurringIncome extends Model
{
    protected $fillable = [
        'user_id',
        'income_source_id',
        'amount',
        'frequency',
        'start_date',
        'end_date',
        'next_run_date',
        'active'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'next_run_date' => 'date',
        'active' => 'boolean',
    ];

    public function incomeSource()
    {
        return $this->belongsTo(IncomeSource::class);
    }
}
