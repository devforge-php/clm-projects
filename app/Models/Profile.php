<?php

namespace App\Models;

use App\Events\ProfileUpdated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Profile extends Model
{
    use HasFactory;
 
    protected $fillable =  ['user_id', 'gold', 'tasks', 'refferals', 'level'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted()
    {
        static::updated(function ($profile) {
            // Agar faqat level o'zgargan bo'lsa, eventni chaqirmaymiz
            if ($profile->wasChanged('level')) {
                return;
            }

            // Cache'ni tozalash
            Cache::flush(); // Barcha cache'ni tozalash

            event(new ProfileUpdated($profile));
        });

        static::created(function ($profile) {
            // Yangi profil qo'shilganda ham cache'ni tozalash
            Cache::flush(); // Barcha cache'ni tozalash
        });
    }
}
