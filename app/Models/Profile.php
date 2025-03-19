<?php

namespace App\Models;

use App\Events\ProfileUpdated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;
 
    protected $fillable =  ['user_id', 'gold', 'silver', 'diamond', 'level'];

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

            event(new ProfileUpdated($profile));
        });
    }
} 
