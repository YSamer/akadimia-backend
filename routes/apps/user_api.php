<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BatchController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\UserAuthController;
use App\Http\Controllers\BatchApplyController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentController;
// use App\Http\Controllers\LastGroupWirdConfigController;
// use App\Http\Controllers\LastGroupWirdController;
// use App\Http\Controllers\LastWirdController;
// use App\Http\Controllers\LastWirdDoneController;


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
        Route::get('profile', [UserAuthController::class, 'profile']);
        Route::post('update-profile', [UserAuthController::class, 'updateProfile']);

        // Batches
        Route::get('batches', [BatchController::class, 'index']);
        Route::get('home-batches', [BatchController::class, 'homeBatches']);
        Route::get('batches/{id}', [BatchController::class, 'show']);
        Route::post('batch/apply', [BatchApplyController::class, 'apply']);
        Route::post('batch/achieve', [BatchApplyController::class, 'achieve']);
        Route::post('batch/unachieve', [BatchApplyController::class, 'unachieve']);


        // Groups
        Route::get('groups', [GroupController::class, 'index']);
        Route::get('groups/{id}', [GroupController::class, 'show']);

        // // Wirds
        // Route::get('today-wirds/{id}', [WirdController::class, 'groupTodayWirdsStudent']);
        // Route::post('wird-done', [WirdDoneController::class, 'wirdDone']);
        // Route::post('wird-not-done', [WirdDoneController::class, 'wirdDone']);

        // Exams
        Route::get('/get-exams', [ExamController::class, 'getUserExams']);
        Route::get('/get-exam/{id}', [ExamController::class, 'getExamDetails']);
        Route::post('/send-responses', [ExamController::class, 'submitResponse']);

        // Payment 
        Route::post('/achieve-payment', [PaymentController::class, 'achievePayment']);
        Route::post('/create-payment', [PaymentController::class, 'createPayment']);

        // Notifications
        Route::get('/get-notifications', [NotificationController::class, 'getNotifications']);
        Route::post('/read-notification/{id}', [NotificationController::class, 'readNotification']);
        Route::post('/delete-notification/{id}', [NotificationController::class, 'deleteNotification']);

    });
});