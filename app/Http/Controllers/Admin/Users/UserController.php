<?php
namespace App\Http\Controllers\Admin\Users;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Http\Resources\UserProfileResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class UserController extends Controller
{
    /**
     * Index - barcha foydalanuvchilarni olish, cache'langan.
     */
    public function index(Request $request)
    {
        // Paginationni 10 ta element bilan amalga oshirish
        $perPage = $request->get('perPage', 10);

        // Cache yordamida foydalanuvchilarni olish, saralash va paginate qilish
        $profiles = Cache::remember("profiles_{$perPage}", 60, function () use ($perPage) {
            return Profile::with('user') // User bilan birga olish
                ->orderByDesc('gold') // gold bo'yicha kamayish tartibida
                ->orderByDesc('level') // level bo'yicha kamayish tartibida
                ->orderByDesc('refferals') // refferals bo'yicha kamayish tartibida
                ->orderByDesc('tasks') // tasks bo'yicha kamayish tartibida
                ->paginate($perPage); // Pagination qo'shish
        });

        return UserProfileResource::collection($profiles); // Resurs orqali yuborish
    }

    /**
     * Show - bitta foydalanuvchini olish, cache'langan.
     */
    public function show($userId)
    {
        // user_id orqali profilni olish
        $profile = Cache::remember("profile_user_{$userId}", 60, function () use ($userId) {
            return Profile::with('user')->where('user_id', $userId)->firstOrFail();
        });
    
        return new UserProfileResource($profile);
    }
    

    /**
     * Delete - foydalanuvchini o'chirish va cache'ni yangilash.
     */
    public function destroy($userId)
    {
        $profile = Profile::where('user_id', $userId)->firstOrFail();
        $user = $profile->user;
    
        $profile->delete();
        $user->delete();
    
        Cache::forget("profile_user_{$userId}");
        Cache::forget("user_{$userId}");
        Cache::forget('profiles');
        Cache::forget('users');
    
        return response()->json(['message' => 'Foydalanuvchi va uning profili o\'chirildi!']);
    }
    

}
