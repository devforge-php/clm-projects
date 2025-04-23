<?php

use App\Http\Controllers\Admin\SocialMediaUserNames\SocialUserNamesController;
use App\Http\Controllers\Admin\Tasks\TaskController;
use App\Http\Controllers\Admin\UserResetController;
use App\Http\Controllers\Admin\Users\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::middleware('throttle:60,1')->group(function () {
        Route::get('/tasks', [TaskController::class, 'index']);         // GET: barcha tasklar
        Route::get('/tasks/{id}', [TaskController::class, 'show']);     // GET: bitta task
        Route::post('/tasks', [TaskController::class, 'store']);        // POST: task yaratish
        Route::post('/tasks/{id}', [TaskController::class, 'update']);  // POST: update uchun (PUT emas)
        Route::delete('/tasks/{id}', [TaskController::class, 'destroy']); // DELETE: task o‘chirish
    });
   Route::get('user', [UserController::class, 'index']);
   Route::get('user/{id}', [UserController::class, 'show'])->name('show');
   Route::delete('user/{id}', [UserController::class, 'destroy']);
    Route::get('SocialMedia', [SocialUserNamesController::class, 'index'])->middleware('throttle:30,1');

    Route::post('reset-users', [UserResetController::class, 'resetAllUsers'])->middleware('throttle:5,1');
});
