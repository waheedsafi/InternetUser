<?php

use App\Http\Controllers\api\app\Directorate\DirectorateController;
use App\Http\Controllers\api\app\EmploymentType\EmploymentTypeController;
use App\Http\Controllers\api\app\InternetUser\InternetUserController;
use App\Http\Controllers\Api\App\Person\PersonController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\api\app\Device_type\DeviceTypeController;
use App\Http\Controllers\api\app\Violation\ViolationTypeController;
use Illuminate\Support\Facades\Route;
Route::post('/login', [AuthController::class, 'login']);
Route::get('/internet',[InternetUserController::class, 'index']);
Route::post('/internet',[InternetUserController::class,'store']); 
Route::get('/employment-type',[EmploymentTypeController::class,'index']);
Route::get('/directorate',[DirectorateController::class,'index']);
Route::get('/device-types', [DeviceTypeController::class, 'index']);
// Route for violation 
Route::get('/violation',[ViolationTypeController::class,'index']);
Route::post('/violation',[ViolationTypeController::class, 'store']);
Route::delete('/violation/{id}',[ViolationTypeController::class,' destroy']);
Route::put('/violation/{id}',[ViolationTypeController::class,'edit']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::put('/update-profile/{id}', [AuthController::class, 'updateProfile']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']);
});
