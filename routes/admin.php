<?php

use App\Http\Controllers\Admin\Posts\PostController;
use App\Http\Controllers\Admin\SocialMediaUserNames\SocialUserNamesController;
use App\Http\Controllers\Admin\Tasks\TaskController;
use App\Http\Controllers\Admin\UserResetController;
use App\Http\Controllers\Admin\Users\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    
    // Throttled routes
    Route::middleware('throttle:60,1')->group(function () {
        // Tasks
        Route::get('/tasks', [TaskController::class, 'index']);
        Route::get('/tasks/{id}', [TaskController::class, 'show']);
        Route::post('/tasks', [TaskController::class, 'store']);
        Route::post('/tasks/{id}', [TaskController::class, 'update']);
        Route::delete('/tasks/{id}', [TaskController::class, 'destroy']);

        // Posts
        Route::get('/posts', [PostController::class, 'index']);
        Route::get('/posts/{id}', [PostController::class, 'show']);
        Route::post('/posts', [PostController::class, 'store']);
        Route::post('/posts/{id}', [PostController::class, 'update']);
        Route::delete('/posts/{id}', [PostController::class, 'destroy']);
    });

    // No throttle
    Route::get('user', [UserController::class, 'index']);
    Route::get('user/{id}', [UserController::class, 'show'])->name('show');
    Route::delete('user/{id}', [UserController::class, 'destroy']);
    Route::get('SocialMedia', [SocialUserNamesController::class, 'index'])->middleware('throttle:30,1');

    Route::post('reset-users', [UserResetController::class, 'resetAllUsers'])->middleware('throttle:5,1');
});