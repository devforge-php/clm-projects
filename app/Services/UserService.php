<?php 

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;

class UserService
{
    /**
     * Get paginated users from cache or database.
     */
    public function getUsers(int $page = 1)
    {
        $cacheKey = "users_page_{$page}";

        return Cache::remember($cacheKey, 60, function () use ($page) {
            return User::with('profile')
                ->orderByDesc('profile.gold')  // 1. Gold bo'yicha saralash
                ->orderByDesc('profile.tasks')  // 2. Tasks bo'yicha
                ->orderByDesc('profile.refferals')  // 3. Refferals bo'yicha
                ->orderByDesc('profile.level')  // 4. Level bo'yicha
                ->paginate(10);
        });
    }

    /**
     * Get a single user by ID (with caching).
     */
    public function getUserById(string $id)
    {
        return Cache::remember("user_{$id}", 60, function () use ($id) {
            return User::with('profile')->findOrFail($id);
        });
    }

    /**
     * Delete a user and refresh cache.
     */
    public function deleteUser(string $id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        // Cache tozalash
        Cache::forget("user_{$id}");
        $this->clearUserPagesCache();
    }

    /**
     * Clear all user pagination cache.
     */
    private function clearUserPagesCache()
    {
        $page = 1;
        while (Cache::has("users_page_{$page}")) {
            Cache::forget("users_page_{$page}");
            $page++;
        }
    }
}
