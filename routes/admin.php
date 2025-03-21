<?php

use App\Http\Controllers\Admin\SocialMediaUserNames\SocialUserNamesController;
use App\Http\Controllers\Admin\Tasks\TaskController;
use App\Http\Controllers\Admin\Users\UserController;
use Illuminate\Support\Facades\Route;





Route::middleware(['auth:sanctum', 'role:admin'])->group( function () {
    Route::resource('tasks', TaskController::class);
    Route::resource('users', UserController::class);
    Route::get('SocialMedia', [SocialUserNamesController::class, 'index']);
    });