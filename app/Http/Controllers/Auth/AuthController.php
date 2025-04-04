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
        try {
            // Registering the user
            $this->authServices->register($request->validated());

            return response()->json([
                'message' => 'User registration in process'
            ], 202);

        } catch (\Exception $e) {
            // Return the error message with 500 status code
            return response()->json([
                'error' => 'Something went wrong during registration: ' . $e->getMessage()
            ], 500);
        }
    }

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $token = $this->authServices->login($request->validated());

            if (!$token) {
                return response()->json(['error' => 'Invalid credentials'], 401);
            }

            // Getting the logged-in user's role
            $user = auth()->user();
            $role = $user->role; // Assuming user has a role field

            return response()->json([
                'message' => 'Login successful',
                'token' => $token,
                'role' => $role // Returning the role
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Login failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        try {
            $this->authServices->logout();
            return response()->json(['message' => 'Logout successful']);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Logout failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteAccount(Request $request): JsonResponse
    {
        try {
            $this->authServices->deleteAccount();
            return response()->json(['message' => 'Account deleted successfully']);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to delete account: ' . $e->getMessage()
            ], 500);
        }
    }
}
