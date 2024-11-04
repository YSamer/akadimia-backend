<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserAuthController;
use App\Http\Controllers\AdminAuthController;

// User Auth 
Route::get('admin/verify/{id}/{hash}', [AdminAuthController::class, 'verify'])->name('admin.verification.verify');
Route::get('/email/verify/{id}/{hash}', [UserAuthController::class, 'verify'])->name('user.verification.verify');

Route::prefix('user')->group(function () {
    Route::post('register', [UserAuthController::class, 'register']);
    Route::post('login', [UserAuthController::class, 'login']);
    Route::post('re-send-otp', [UserAuthController::class, 'reSendOtp']);
    Route::post('forgot-password', [UserAuthController::class, 'forgotPassword']);
    Route::post('verify-otp-reset', [UserAuthController::class, 'verifyOtpForPasswordReset']);
    Route::post('reset-password', [UserAuthController::class, 'resetPassword']);
    Route::post('verify-otp', [UserAuthController::class, 'verifyOtp']);

    Route::middleware('auth:user')->group(function () {
        Route::get('auto-login', [UserAuthController::class, 'autoLogin']);
        Route::get('logout', [UserAuthController::class, 'logout']);
        Route::post('update-profile', [UserAuthController::class, 'updateProfile']);
    });
});
Route::prefix('admin')->group(function () {
    Route::post('register', [AdminAuthController::class, 'register']);
    Route::post('login', [AdminAuthController::class, 'login']);
    Route::post('forgot-password', [AdminAuthController::class, 'forgotPassword']);
    Route::post('verify-otp-reset', [AdminAuthController::class, 'verifyOtpForPasswordReset']);
    Route::post('reset-password', [AdminAuthController::class, 'resetPassword']);
    Route::post('verify-otp', [AdminAuthController::class, 'verifyOtp']);

    Route::middleware('auth:admin')->group(function () {
        Route::get('auto-login', [AdminAuthController::class, 'autoLogin']);
        Route::get('logout', [AdminAuthController::class, 'logout']);
        Route::post('update-profile', [AdminAuthController::class, 'updateProfile']);
    });
});

