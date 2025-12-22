<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvestmentValuation extends Model
{
    protected $fillable = [
        'investment_id',
        'valuation_amount',
        'valuation_date',
        'notes',
    ];

    protected $casts = [
        'valuation_date' => 'date',
        'valuation_amount' => 'decimal:2',
    ];

    public function investment()
    {
        return $this->belongsTo(Investment::class);
    }
}
