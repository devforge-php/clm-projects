<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use Illuminate\Http\Request;

class UserResetController extends Controller
{
    public function resetAllUsers()
    {
        // Barcha foydalanuvchilarni yangilash
        Profile::query()->update([
            'gold' => 0,
            'tasks' => 0,
            'refferals' => 0,
            'level' => 0,
        ]);

        return response()->json(['message' => 'Barcha foydalanuvchilar reset qilindi.']);
    }
}
