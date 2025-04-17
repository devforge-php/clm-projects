<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'click_trans_id',
        'merchant_trans_id',
        'amount',
        'status',
        'user_id',
        'gold_amount'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}