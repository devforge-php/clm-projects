<?php

namespace App\GraphQL\Queries;

use Illuminate\Support\Facades\DB;
use App\Models\User;

class UserQuery
{
    public function topUsers()
    {
        return User::join('profiles', 'users.id', '=', 'profiles.user_id')
            ->orderByDesc('profiles.gold')
            ->take(10)
            ->get(['users.id', 'users.firstname', 'users.lastname', 'users.username', 'users.phone']);
    }
}