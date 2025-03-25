<?php

use App\Http\Controllers\Admin\Tasks\TaskController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Click\PaymentController;
use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\Referral\ReferralController;
use App\Http\Controllers\SocailMedia\SocialMediaController;
use App\Http\Controllers\TaskController as ControllersTaskController;
use App\Http\Controllers\UserTasks\UserTaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;





// Auth start

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::delete('delete-account', [AuthController::class, 'deleteAccount']); // Yangi yo‘l qo‘shildi
});

    // Auth end

// Profile start
Route::middleware(['auth:sanctum'])->group( function () {
    Route::get('profile', [ProfileController::class, 'show']);
    Route::post('profileImage', [ProfileController::class, 'update']);

    Route::get('socialMedia', [SocialMediaController::class, 'index']);
    Route::post('socialMedia', [SocialMediaController::class, 'store']);
    Route::put('socialMedia/{id}', [SocialMediaController::class, 'update']);
   
}); 
// Profile end




// Referral Start
Route::middleware(['auth:sanctum'])->group( function () {
    Route::get('referral', [ReferralController::class, 'index']);
    Route::post('referral-code', [ReferralController::class, 'useReferralCode']);
}); 
// Referral End

// Click start
// Click start

// To'lovni boshlash (faqat autentifikatsiya qilingan foydalanuvchilar)
Route::middleware(['auth:sanctum'])->post('/payment/initiate', [PaymentController::class, 'initiatePayment']);

// Callback ochiq bo'lishi kerak, chunki Click serveri login qilmagan holda so'rov yuboradi
Route::post('/payment/callback', [PaymentController::class, 'paymentCallback'])->name('payment.callback');

// Click end


// takst start
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/tasks', [UserTaskController::class, 'index']);
    Route::post('/tasks/verify', [UserTaskController::class, 'verifyTask']);
});
// takst end