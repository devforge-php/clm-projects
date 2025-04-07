<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'gold',
        'tasks',
        'refferals',
        'image',  // 'image' maydoni qo'shildi
    ];

    protected $casts = [
        'gold'      => 'integer',
        'tasks'     => 'integer',
        'refferals' => 'integer',
    ];

    protected $appends = ['level'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getLevelAttribute(): int
    {
        return $this->gold;
    }

    // Rasm uchun accessor qo'shish
    public function getImageUrlAttribute()
    {
        return asset('storage/' . $this->image);
    }
}
