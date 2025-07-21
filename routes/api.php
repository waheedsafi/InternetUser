<?php

use App\Http\Controllers\api\app\InternetUser\InternetUserController;
use App\Http\Controllers\Api\App\Person\PersonController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('authmidd')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->middleware('authmidd:admin');
    Route::put('/update-profile/{id}', [AuthController::class, 'updateProfile']);
    Route::get('/profile', [AuthController::class, 'profile']);
});
