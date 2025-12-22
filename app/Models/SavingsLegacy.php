<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavingsLegacy extends Model
{
    use HasFactory;

    protected $table = 'savings_legacy';

    protected $fillable = [
        'user_id',
        'income_id',
        'category',
        'amount',
        'percentage_applied',
        'date',
        'month',
        'quarter',
        'year',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'percentage_applied' => 'decimal:2',
        'date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function income()
    {
        return $this->belongsTo(Income::class);
    }
}
