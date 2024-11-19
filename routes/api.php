<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BatchController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\UserAuthController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\BatchApplyController;
use App\Http\Controllers\GroupConfigController;

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

        // Batches
        Route::get('batches', [BatchController::class, 'index']);
        Route::get('batches/{id}', [BatchController::class, 'show']);
        Route::post('batch/apply', [BatchApplyController::class, 'apply']);
        Route::post('batch/achieve', [BatchApplyController::class, 'achieve']);
        Route::post('batch/unachieve', [BatchApplyController::class, 'unachieve']);


        // Groups
        Route::get('groups', [GroupController::class, 'index']);
        Route::get('groups/{id}', [GroupController::class, 'show']);
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

        // Batches
        Route::get('batches', [BatchController::class, 'index']);
        Route::get('batches/{id}', [BatchController::class, 'show']);
        Route::post('batches/create', [BatchController::class, 'store']);
        Route::post('batches/update/{id}', [BatchController::class, 'update']);
        Route::post('batches/delete/{id}', [BatchController::class, 'destroy']);

        // Groups
        Route::get('groups', [GroupController::class, 'index']);
        Route::get('groups/{id}', [GroupController::class, 'show']);
        Route::post('groups/create', [GroupController::class, 'store']);
        Route::post('groups/update/{id}', [GroupController::class, 'update']);
        Route::post('groups/delete/{id}', [GroupController::class, 'destroy']);

        // Group Configs
        Route::get('group-configs', [GroupConfigController::class, 'index']);
        Route::get('group-configs/{id}', [GroupConfigController::class, 'show']);
        Route::post('group-configs/create', [GroupConfigController::class, 'store']);
        Route::post('group-configs/update/{id}', [GroupConfigController::class, 'update']);
        Route::post('group-configs/delete/{id}', [GroupConfigController::class, 'destroy']);
    });
});

