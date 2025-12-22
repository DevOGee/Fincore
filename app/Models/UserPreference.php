<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPreference extends Model
{
    protected $fillable = [
        'user_id',
        'currency',
        'timezone',
        'financial_year_start',
        'language',
        'savings_percentage',
        'risk_profile',
        'budget_strategy',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
