<?php 

namespace App\Services;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class ProfileServices
{
    public function getProfileByUserId($userId)
    {
        // Lazy loading orqali profilni olish
        return Profile::where('user_id', $userId)->with('user')->first();
    }

    public function updateUserProfile($userId, $data)
    {
        $user = User::findOrFail($userId);
        $user->update($data);

        // Yangilangan profilni olish
        $profile = $user->profile->load('user');

        return $user;
    }
}
