<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Saving extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'user_id',
        'name',
        'balance',
        'target_amount',
        'target_amount',
        'description',
        'date',
        'month',
        'year',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
