<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BatchController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\AppController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\HalaqahController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\TeacherAuthController;
// use App\Http\Controllers\LastGroupWirdConfigController;
// use App\Http\Controllers\LastGroupWirdController;
// use App\Http\Controllers\LastWirdController;
// use App\Http\Controllers\LastWirdDoneController;


Route::prefix('teacher')->group(function () {
    // Route::post('register', [TeacherAuthController::class, 'register']);
    Route::post('login', [TeacherAuthController::class, 'login']);
    Route::post('re-send-otp', [TeacherAuthController::class, 'reSendOtp']);
    Route::post('forgot-password', [TeacherAuthController::class, 'forgotPassword']);
    Route::post('verify-otp-reset', [TeacherAuthController::class, 'verifyOtpForPasswordReset']);
    Route::post('reset-password', [TeacherAuthController::class, 'resetPassword']);
    Route::post('verify-otp', [TeacherAuthController::class, 'verifyOtp']);

    Route::middleware('auth:teacher')->group(function () {
        Route::get('auto-login', [TeacherAuthController::class, 'autoLogin']);
        Route::get('logout', [TeacherAuthController::class, 'logout']);
        Route::get('profile', [TeacherAuthController::class, 'profile']);
        Route::post('update-profile', [TeacherAuthController::class, 'updateProfile']);

        // Batches
        Route::get('batches', [BatchController::class, 'indexAdmin']);
        Route::get('all-batches', [BatchController::class, 'indexAll']);
        Route::get('batches/{id}', [BatchController::class, 'show']);
        // Route::post('batches/create', [BatchController::class, 'store']);
        // Route::post('batches/update/{id}', [BatchController::class, 'update']);
        // Route::post('batches/delete/{id}', [BatchController::class, 'destroy']);

        // Groups
        Route::get('groups', [GroupController::class, 'index']);
        Route::get('all-groups', [GroupController::class, 'indexAll']);
        Route::get('groups/{id}', [GroupController::class, 'show']);
        // Route::post('groups/create', [GroupController::class, 'store']);
        // Route::post('groups/update/{id}', [GroupController::class, 'update']);
        // Route::post('groups/delete/{id}', [GroupController::class, 'destroy']);

        // // Group Wird Configs
        // Route::get('group-wird-configs', [GroupWirdConfigController::class, 'index']);
        // Route::get('group-wird-configs/{id}', [GroupWirdConfigController::class, 'show']);
        // Route::post('group-wird-configs/create', [GroupWirdConfigController::class, 'store']);
        // Route::post('group-wird-configs/update/{id}', [GroupWirdConfigController::class, 'update']);
        // Route::post('group-wird-configs/delete/{id}', [GroupWirdConfigController::class, 'destroy']);

        // // Wird 
        // Route::post('set-today-wird', [WirdController::class, 'setTodayWirds']);
        // Route::get('wirds', [WirdController::class, 'index']);
        // Route::get('today-wirds', [WirdController::class, 'todayWirds']);
        // Route::get('today-wirds/{id}', [WirdController::class, 'groupTodayWirds']);

        // Exams
        Route::get('/get-exams', [ExamController::class, 'getExams']);
        Route::get('/get-exam/{id}', [ExamController::class, 'getExamDetails']);
        Route::post('/create-exam', [ExamController::class, 'createExam']);
        Route::post('/add-questions', [ExamController::class, 'addQuestions']);
        Route::post('/edit-exam/{id}', [ExamController::class, 'editExam']);
        Route::post('/delete-question/{id}', [ExamController::class, 'deleteQuestion']);
        Route::get('/view-responses/{examId}', [ExamController::class, 'viewResponses']);

        // Payments
        Route::get('/all-payments', [PaymentController::class, 'getAllPayments']);
        Route::post('/confirm-payment/{id}', [PaymentController::class, 'confirmPayment']);

        // Notifications
        Route::get('/get-notifications', [NotificationController::class, 'getNotifications']);
        Route::post('/read-notification/{id}', [NotificationController::class, 'readNotification']);
        Route::post('/delete-notification/{id}', [NotificationController::class, 'deleteNotification']);

        // Global
        Route::get('all-groups', [GroupController::class, 'indexAll']);
        Route::get('all-users', [AppController::class, 'allUsers']);
        Route::get('all-group-users/{id}', [AppController::class, 'allGroupUsers']);

        // Halaqah
        Route::post('finish-halaqah', [HalaqahController::class, 'finishHalaqah']);
    });
});
