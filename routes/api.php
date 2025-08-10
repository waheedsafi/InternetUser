<?php

use App\Http\Controllers\api\app\Directorate\DirectorateController;
use App\Http\Controllers\api\app\EmploymentType\EmploymentTypeController;
use App\Http\Controllers\api\app\InternetUser\InternetUserController;
use App\Http\Controllers\Api\App\Person\PersonController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\api\app\Device_type\DeviceTypeController;
use App\Http\Controllers\api\app\Violation\ViolationTypeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\app\Violation\ViolationController;
use App\Http\Controllers\Api\Group\GroupController;

Route::post('/login', [AuthController::class, 'login']);
Route::get('/internet',[InternetUserController::class, 'index']);
Route::get('/groups',[GroupController::class,'index']);
Route::get('/user',[AuthController::class,'index']);
Route::put('/user/{id}', [AuthController::class, 'systemUsersUpdate']);
Route::delete('/user/{id}', [AuthController::class, 'systemUsersDelete']);
Route::post('/internet',[InternetUserController::class,'store']);
 Route::put('/internet/{id}', [InternetUserController::class, 'update']); 
 Route::post('/check-username', [InternetUserController::class, 'checkUsername']);
 Route::post('/check-email-of-internet-users', [InternetUserController::class, 'checkEmailInternetUser']);
 Route::post('/check-phone-of-internet-user', [InternetUserController::class, 'checkPhoneOfInternetUsers']);
 Route::post('/check-mac-address', [InternetUserController::class, 'checkMacAddress']);
Route::delete('/internet/{id}',[InternetUserController::class,'destroy']); 
Route::get('/employment-type',[EmploymentTypeController::class,'index']);
Route::get('/directorate',[DirectorateController::class,'index']);
Route::get('/device-types', [DeviceTypeController::class, 'index']);
Route::put('/users/{id}/status', [InternetUserController::class,'updateStatus']);
Route::get('/employment-type-counts',[EmploymentTypeController::class,'employmentTypeCounts']);
Route::get('/total-users', [InternetUserController::class, 'getTotalUsers']);
Route::get('/violation',[ViolationTypeController::class,'index']);
Route::post('/violation',[ViolationTypeController::class, 'store']);
Route::delete('/violation/{id}', [ViolationTypeController::class,'destroy']);
Route::put('/violation/{id}',[ViolationTypeController::class,'update']);
Route::post('/violationOnaUser', [ViolationController::class, 'store']);
Route::get('/allViolationsFromUsers', [ViolationController::class, 'index']);
Route::post('/check-email', [AuthController::class, 'checkEmail']);
Route::middleware('auth:sanctum')->group(function () {
    Route::put('/update-profile/{id}', [AuthController::class, 'updateProfile']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']);
Route::post('/register', [AuthController::class, 'register']);
});