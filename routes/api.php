<?php

use App\Http\Controllers\api\app\Directorate\DirectorateController;
use App\Http\Controllers\api\app\EmploymentType\EmploymentTypeController;
use App\Http\Controllers\api\app\InternetUser\InternetUserController;
use App\Http\Controllers\Api\App\Person\PersonController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\api\app\Device_type\DeviceTypeController;
use App\Http\Controllers\api\app\Violation\ViolationTypeController;
use App\Http\Controllers\api\template\user\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::get('/internet', [InternetUserController::class, 'index']);
Route::get('/user', [AuthController::class, 'index']);
Route::post('/internet', [InternetUserController::class, 'store']);
Route::put('/internet/{id}', [InternetUserController::class, 'update']);
Route::delete('/internet/{id}', [InternetUserController::class, 'destroy']);
Route::get('/employment-type', [EmploymentTypeController::class, 'index']);
Route::get('/directorate', [DirectorateController::class, 'index']);
Route::get('/device-types', [DeviceTypeController::class, 'index']);
Route::put('users/{id}/status', [InternetUserController::class, 'updateStatus']);
Route::get('/employment-type-counts', [EmploymentTypeController::class, 'employmentTypeCounts']);
Route::get('/total-users', [InternetUserController::class, 'getTotalUsers']);
Route::get('/violation', [ViolationTypeController::class, 'index']);
Route::post('/violation', [ViolationTypeController::class, 'store']);
Route::delete('/violation/{id}', [ViolationTypeController::class, 'destroy']);
Route::put('/violation/{id}', [ViolationTypeController::class, 'update']);
Route::post('/register', [UserController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::put('/update-profile/{id}', [AuthController::class, 'updateProfile']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']);
});
