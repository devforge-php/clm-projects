<?php

use App\Http\Controllers\Admin\Tasks\TaskController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Click\PaymentController;
use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\Profile\ProfileImageController;
use App\Http\Controllers\Referral\ReferralController;
use App\Http\Controllers\SocailMedia\SocialMediaController;
use App\Http\Controllers\UserTasks\UserTaskController;
use Illuminate\Support\Facades\Route;

// Public routes: registration and login with DDoS protection
Route::middleware(['throttle:78,1'])->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});

// Protected routes under auth:sanctum and DDoS throttle
Route::middleware(['auth:sanctum', 'throttle:78,1'])->group(function () {
    // Auth actions
    Route::post('logout', [AuthController::class, 'logout']);
    Route::delete('delete-account', [AuthController::class, 'deleteAccount']);

    // Profile management
    Route::get('profile', [ProfileController::class, 'show']);
    Route::post('profile', [ProfileController::class, 'updateProfile']);

    // Social Media
    Route::get('socialMedia', [SocialMediaController::class, 'index']);
    Route::post('socialMedia', [SocialMediaController::class, 'store']);
    Route::patch('socialMedia/{id}', [SocialMediaController::class, 'update']);

    // Profile Image
    Route::get('profileImage', [ProfileImageController::class, 'index']);
    Route::post('profileImage', [ProfileImageController::class, 'update']);
    Route::delete('profileImage', [ProfileImageController::class, 'destroy']);

    // Referral
    Route::get('referral', [ReferralController::class, 'index']);
    Route::post('referral-code', [ReferralController::class, 'useReferralCode']);

    // User Tasks
    Route::get('/tasks', [UserTaskController::class, 'index']);
    Route::post('/tasks/verify', [UserTaskController::class, 'verifyTask']);

    // Payment initiation
    Route::post('/payment/initiate', [PaymentController::class, 'initiatePayment']);
    Route::post('/payment/callback', [PaymentController::class, 'paymentCallback'])
        ->middleware('throttle:78,1')
        ->name('payment.callback');
});

// yangi routelar uchun 
