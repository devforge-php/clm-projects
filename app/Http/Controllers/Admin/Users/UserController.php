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
    public function show($id)
    {
        // Foydalanuvchini ID bo'yicha cache'dan olish
        $profile = Cache::remember("profile_{$id}", 60, function () use ($id) {
            return Profile::with('user')->findOrFail($id); // Foydalanuvchini topish
        });

        return new UserProfileResource($profile); // Resurs orqali yuborish
    }

    /**
     * Delete - foydalanuvchini o'chirish va cache'ni yangilash.
     */
    public function destroy($id)
    {
        $profile = Profile::findOrFail($id);
        $user = $profile->user; // Foydalanuvchini olish

        // Profile va userni o'chirish
        $profile->delete();
        $user->delete();

        // Cache'dan profile va userni o'chirish
        Cache::forget("profile_{$id}");
        Cache::forget("user_{$user->id}");

        // Barcha profiles va users cache'larini o'chirish
        Cache::forget('profiles');
        Cache::forget('users');

        return response()->json(['message' => 'Foydalanuvchi va uning profili o\'chirildi!']);
    }
}
