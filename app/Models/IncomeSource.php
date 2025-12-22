<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncomeSource extends Model
{
    protected $fillable = ['user_id', 'name', 'type', 'linked_account'];

    public function incomes()
    {
        return $this->hasMany(Income::class);
    }

    public function recurringIncomes()
    {
        return $this->hasMany(RecurringIncome::class);
    }
}
