<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'gold',
        'tasks',
        'refferals',
        'image',
    ];

    protected $casts = [
        'gold'      => 'integer',
        'tasks'     => 'integer',
        'refferals' => 'integer',
    ];

    protected $appends = ['level', 'image_url'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getLevelAttribute(): int
    {
        return $this->gold;
    }

    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }
    protected static function booted()
{
    static::saved(function ($profile) {
        Cache::forget("profile_image_{$profile->user_id}");
    });

    static::deleted(function ($profile) {
        Cache::forget("profile_image_{$profile->user_id}");
    });
}

}
