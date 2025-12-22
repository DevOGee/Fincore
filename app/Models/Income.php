<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Income extends Model
{
    use Auditable, SoftDeletes;
    protected $fillable = [
        'user_id',
        'income_source_id',
        'source',
        'amount',
        'date',
        'category',
        'account_credited',
        'description'
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function incomeSource()
    {
        return $this->belongsTo(IncomeSource::class);
    }
}
