<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'quantity',
        'amount',
        'status',
        'transaction_param',  // Include transaction_param
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Method to mark payment as paid
    public function markAsPaid()
    {
        $this->status = 'paid';
        $this->save();
    }
}
