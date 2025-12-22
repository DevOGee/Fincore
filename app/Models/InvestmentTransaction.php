<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvestmentTransaction extends Model
{
    protected $fillable = [
        'investment_id',
        'type',
        'amount',
        'transaction_date',
        'source',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function investment()
    {
        return $this->belongsTo(Investment::class);
    }
}
