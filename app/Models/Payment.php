<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'quantity',
        'amount',
        'click_payment_id',  // eski transaction_id o‘rniga
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
