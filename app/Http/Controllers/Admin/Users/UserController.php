<?php

namespace App\Http\Controllers\Admin\Users;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserProfileResource;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display a paginated list of users.
     */
    public function index(Request $request)
    {
        $page = $request->get('page', 1);
        $users = $this->userService->getUsers($page);

        return response()->json(UserProfileResource::collection($users));
    }

    /**
     * Display a single user.
     */
    public function show(string $id)
    {
        $user = $this->userService->getUserById($id);
        return response()->json(new UserProfileResource($user));
    }

    /**
     * Remove a user and refresh cache.
     */
    public function destroy(string $id)
    {
        $user = $this->userService->getUserById($id);
        $this->userService->deleteUser($id);

        return response()->json([
            'message' => 'User deleted successfully',
            'deleted_user' => new UserProfileResource($user)
        ]);
    }
}
