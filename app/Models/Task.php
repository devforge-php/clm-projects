<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = ['image', 'telegram', 'instagram', 'youtube', 'twitter', 'text', 'number','reward', ];

    
}
