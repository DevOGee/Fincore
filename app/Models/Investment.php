<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Investment extends Model
{
    use Auditable, SoftDeletes;
    protected $fillable = [
        'user_id',
        'name',
        'type',
        'status',
        'start_date',
        'initial_investment',
        'current_value',
        'details',
    ];

    protected $casts = [
        'start_date' => 'date',
        'details' => 'array',
        'initial_investment' => 'decimal:2',
        'current_value' => 'decimal:2',
    ];

    public function transactions()
    {
        return $this->hasMany(InvestmentTransaction::class);
    }

    public function valuations()
    {
        return $this->hasMany(InvestmentValuation::class);
    }

    public function getTotalInvestedAttribute()
    {
        $contributions = $this->transactions()->whereIn('type', ['buy', 'add'])->sum('amount');
        $withdrawals = $this->transactions()->where('type', 'withdraw')->sum('amount');
        return $this->initial_investment + $contributions - $withdrawals;
    }

    public function getRoiAttribute()
    {
        $totalInvested = $this->total_invested;
        if ($totalInvested == 0)
            return 0;
        return ($this->current_value - $totalInvested) / $totalInvested * 100;
    }

    public function getTotalGainLossAttribute()
    {
        return $this->current_value - $this->total_invested;
    }
}
