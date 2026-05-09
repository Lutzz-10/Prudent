<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionItem extends Model
{
    protected $fillable = ['transaction_id', 'item_name', 'qty', 'unit_price', 'subtotal'];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}