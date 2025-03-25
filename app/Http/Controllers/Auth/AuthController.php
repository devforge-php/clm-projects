<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthServices;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected $authServices;

    public function __construct(AuthServices $authServices)
    {
        $this->authServices = $authServices;
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $user = $this->authServices->register($request->validated());

        return response()->json([
            'message' => 'User registration in process',
            'user' => $user
        ], 202);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $token = $this->authServices->login($request->validated());

        if (!$token) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        return response()->json([
            'message' => 'Login successful',
            'token' => $token
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authServices->logout();
        return response()->json(['message' => 'Logout successful']);
    }
    public function deleteAccount(Request $request): JsonResponse
{
    $this->authServices->deleteAccount();
    return response()->json(['message' => 'Account deleted successfully']);
}

}
