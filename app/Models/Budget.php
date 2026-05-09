<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    protected $fillable = ['user_id', 'category_id', 'amount', 'month', 'year'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Hitung total pengeluaran bulan ini untuk budget ini
    public function getSpentAttribute(): float
    {
        return Transaction::where('user_id', $this->user_id)
            ->where('category_id', $this->category_id)
            ->whereMonth('transaction_date', $this->month)
            ->whereYear('transaction_date', $this->year)
            ->sum('total_amount');
    }

    public function getPercentageAttribute(): float
    {
        if ($this->amount == 0) return 0;
        return min(100, ($this->spent / $this->amount) * 100);
    }
}