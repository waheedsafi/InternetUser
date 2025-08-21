<?php

use App\Http\Controllers\api\app\Account\AccountActivationController;
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

Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::middleware('auth:sanctum')->group(function () {
    Route::put('/update-profile/{id}', [AuthController::class, 'updateProfile'])->middleware('check.access:UpdateUsers');
    Route::get('/profile', [AuthController::class, 'profile'])->middleware('check.access:ViewUsers');
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/register', [AuthController::class, 'register'])->middleware('check.access:CreateUsers');
    Route::get('/internet', [InternetUserController::class, 'index']);
    Route::get('/groups', [GroupController::class, 'index']);
    Route::get('/group-count', [GroupController::class, 'countsByType']);
    Route::get('/user', [AuthController::class, 'index']);
    Route::put('/user/{id}', [AuthController::class, 'systemUsersUpdate'])->middleware('check.access:UpdateSystemData');
    Route::delete('/user/{id}', [AuthController::class, 'systemUsersDelete'])->middleware('check.access:DeleteSystemData');
    Route::post('/internet', [InternetUserController::class, 'store'])->middleware('check.access:AddSystemData');
    Route::put('/internet/{id}', [InternetUserController::class, 'update'])->middleware('check.access:UpdateSystemData');
    Route::get('/internet-user-edit/{id}',[InternetUserController::class,'edit'])->middleware('check.access:UpdateSystemData');
    Route::post('/check-username', [InternetUserController::class, 'checkUsername']);
    Route::post('/check-email-of-internet-users', [InternetUserController::class, 'checkEmailInternetUser']);
    Route::post('/check-phone-of-internet-user', [InternetUserController::class, 'checkPhoneOfInternetUsers']);
    Route::post('/check-mac-address', [InternetUserController::class, 'checkMacAddress']);
    Route::delete('/internet/{id}', [InternetUserController::class, 'destroy'])->middleware('check.access:DeleteSystemData');
    Route::get('/employment-type', [EmploymentTypeController::class, 'index']);
    Route::get('/directorate', [DirectorateController::class, 'index']);
    Route::get('/device-types', [DeviceTypeController::class, 'index']);
    Route::put('/users/{id}/status', [InternetUserController::class, 'updateStatus'])->middleware('check.access:UpdateSystemData');
    Route::get('/employment-type-counts', [EmploymentTypeController::class, 'employmentTypeCounts']);
    Route::get('/total-users', [InternetUserController::class, 'getTotalUsers']);
    Route::get('/violation', [ViolationTypeController::class, 'index']);
    Route::post('/violation', [ViolationTypeController::class, 'store'])->middleware('check.access:AddSystemData');
    Route::delete('/violation/{id}', [ViolationTypeController::class, 'destroy'])->middleware('check.access:DeleteSystemData');
    Route::put('/violation/{id}', [ViolationTypeController::class, 'update'])->middleware('check.access:UpdateSystemData');
    Route::post('/violationOnaUser', [ViolationController::class, 'store'])->middleware('check.access:AddSystemData');
    Route::get('/allViolationsFromUsers', [ViolationController::class, 'index']);
    Route::post('/check-email', [AuthController::class, 'checkEmail']);
    Route::get('/internet-users-deactivated', [InternetUserController::class, 'getDeactivatedUsernames']);
    Route::post('/account/activate', [AccountActivationController::class, 'activateAccount']);
});
