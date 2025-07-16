<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\LoanController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::prefix('v1')->group(function () {
    // Authentication routes
    Route::prefix('auth')->group(function () {
        Route::post('customer/login', [AuthController::class, 'customerLogin']);
        Route::post('admin/login', [AuthController::class, 'adminLogin']);
        Route::post('customer/register', [AuthController::class, 'customerRegister']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        
        // Protected auth routes
        Route::middleware('auth:api')->group(function () {
            Route::post('logout', [AuthController::class, 'logout']);
            Route::get('me', [AuthController::class, 'me']);
        });
    });

    // Loan public routes
    Route::prefix('loans')->group(function () {
        Route::get('config', [LoanController::class, 'config']);
        Route::post('calculate', [LoanController::class, 'calculate']);
        Route::get('lookup/{phone}', [LoanController::class, 'lookup']);
    });

    // Protected routes
    Route::middleware('auth:api')->group(function () {
        // User routes
        Route::prefix('user')->group(function () {
            Route::get('profile', [UserController::class, 'profile']);
            Route::put('profile', [UserController::class, 'updateProfile']);
            Route::post('change-password', [UserController::class, 'changePassword']);
            Route::post('upload-avatar', [UserController::class, 'uploadAvatar']);
        });

        // Loan routes for customers
        Route::prefix('loans')->group(function () {
            Route::post('apply', [LoanController::class, 'submitApplication']);
            Route::get('my-applications', [LoanController::class, 'myApplications']);
            Route::get('applications/{id}', [LoanController::class, 'applicationDetails']);
        });
    });
});

// Health check
Route::get('health', function () {
    return response()->json([
        'status' => 'OK',
        'timestamp' => now()->toISOString(),
        'version' => '1.0.0'
    ]);
});
