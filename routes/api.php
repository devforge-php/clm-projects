<?php

use App\Http\Controllers\Admin\Tasks\TaskController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Click\PaymentController;
use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\Referral\ReferralController;
use App\Http\Controllers\SocailMedia\SocialMediaController;
use App\Http\Controllers\UserTasks\UserTaskController;
use Illuminate\Support\Facades\Route;

// Auth start
Route::post('register', [AuthController::class, 'register'])->middleware('throttle:10,1');
Route::post('login', [AuthController::class, 'login'])->middleware('throttle:10,1');

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::delete('delete-account', [AuthController::class, 'deleteAccount']);
});
// Auth end

// Profile start
Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    Route::get('profile', [ProfileController::class, 'show']);
    Route::post('profileImage', [ProfileController::class, 'update']);
    Route::post('profile', [ProfileController::class, 'updateprofile']);

    Route::get('socialMedia', [SocialMediaController::class, 'index']);
    Route::post('socialMedia', [SocialMediaController::class, 'store']);
    Route::put('socialMedia/{id}', [SocialMediaController::class, 'update']);
}); 
// Profile end

// Referral Start
Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    Route::get('referral', [ReferralController::class, 'index']);
    Route::post('referral-code', [ReferralController::class, 'useReferralCode']);
});
// Referral End

// Click start (to‘lovlar)
Route::middleware(['auth:sanctum', 'throttle:5,1'])->post('/payment/initiate', [PaymentController::class, 'initiatePayment']);
Route::post('/payment/callback', [PaymentController::class, 'paymentCallback'])->name('payment.callback'); // ochiq qolishi kerak
// Click end

// Tasks start
Route::middleware(['auth:sanctum', 'throttle:30,1'])->group(function () {
    Route::get('/tasks', [UserTaskController::class, 'index']);
    Route::post('/tasks/verify', [UserTaskController::class, 'verifyTask']);
});
// Tasks end
