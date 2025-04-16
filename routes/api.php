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

// Auth start
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::delete('delete-account', [AuthController::class, 'deleteAccount']);
});
// Auth end

// Profile start
Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    Route::get('profile', [ProfileController::class, 'show']);
    Route::post('profile', [ProfileController::class, 'updateprofile']);
    Route::get('socialMedia', [SocialMediaController::class, 'index']);
    Route::post('socialMedia', [SocialMediaController::class, 'store']);
    Route::put('socialMedia/{id}', [SocialMediaController::class, 'update']);
}); 
Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    Route::get('profileImage', [ProfileImageController::class, 'index']);
    Route::post('profileImage', [ProfileImageController::class, 'update']);
    Route::delete('profileImage', [ProfileImageController::class, 'destroy']);
});
// Profile end

// Referral Start
Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    Route::get('referral', [ReferralController::class, 'index']);
    Route::post('referral-code', [ReferralController::class, 'useReferralCode']);
});
// Referral End

Route::middleware(['auth:sanctum', 'throttle:100,1'])
    ->post('/payment/initiate', [PaymentController::class, 'initiatePayment']);
    
Route::match(['get','post'],'/payment/callback',[PaymentController::class,'paymentCallback'])
    ->name('payment.callback');

// Tasks start
Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    Route::get('/tasks', [UserTaskController::class, 'index']);
    Route::post('/tasks/verify', [UserTaskController::class, 'verifyTask']);
});
// Tasks end
