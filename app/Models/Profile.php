<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    // level endi DB-da saqlanmaydi, faqat accessor orqali olinadi
    protected $fillable = [
        'user_id',
        'gold',
        'tasks',
        'refferals',
    ];

    protected $casts = [
        'gold'      => 'integer',
        'tasks'     => 'integer',
        'refferals' => 'integer',
    ];

    // JSON response’larda level ham qaytsin
    protected $appends = ['level', 'image_url'];

    public function getImageUrlAttribute()
    {
        return $this->image
            ? asset('storage/' . $this->image)
            : null;
    }
  
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Har safar $profile->level chaqirilganda gold qiymatini qaytaradi
    public function getLevelAttribute(): int
    {
        return $this->gold;
    }
}
