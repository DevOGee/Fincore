<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpenseCategory extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'type',
        'is_essential',
        'default_budget_cap',
    ];

    protected $casts = [
        'is_essential' => 'boolean',
        'default_budget_cap' => 'decimal:2',
    ];
}
