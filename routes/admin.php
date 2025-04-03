<?php

use App\Http\Controllers\Admin\SocialMediaUserNames\SocialUserNamesController;
use App\Http\Controllers\Admin\Tasks\TaskController;
use App\Http\Controllers\Admin\UserReset\UserResetController;
use App\Http\Controllers\Admin\Users\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::resource('tasks', TaskController::class)->middleware('throttle:60,1');
   Route::get('user', [UserController::class, 'index']);
   Route::get('user/{id}', [UserController::class, 'show']);
   Route::delete('user/{id}', [UserController::class, 'destroy']);
    Route::get('SocialMedia', [SocialUserNamesController::class, 'index'])->middleware('throttle:30,1');

    Route::post('reset-users', [UserResetController::class, 'resetAllUsers'])->middleware('throttle:5,1');
});
