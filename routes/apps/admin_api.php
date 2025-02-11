<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BatchController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AppController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\GroupConfigController;
use App\Http\Controllers\GroupWirdController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentController;
// use App\Http\Controllers\LastGroupWirdConfigController;
// use App\Http\Controllers\LastGroupWirdController;
// use App\Http\Controllers\LastWirdController;
// use App\Http\Controllers\LastWirdDoneController;

Route::prefix('admin')->group(function () {
    // Route::post('register', [AdminAuthController::class, 'register']);
    Route::post('login', [AdminAuthController::class, 'login']);
    Route::post('re-send-otp', [AdminAuthController::class, 'reSendOtp']);
    Route::post('forgot-password', [AdminAuthController::class, 'forgotPassword']);
    Route::post('verify-otp-reset', [AdminAuthController::class, 'verifyOtpForPasswordReset']);
    Route::post('reset-password', [AdminAuthController::class, 'resetPassword']);
    Route::post('verify-otp', [AdminAuthController::class, 'verifyOtp']);

    Route::middleware('auth:admin')->group(function () {
        Route::get('auto-login', [AdminAuthController::class, 'autoLogin']);
        Route::get('logout', [AdminAuthController::class, 'logout']);
        Route::get('profile', [AdminAuthController::class, 'profile']);
        Route::post('update-profile', [AdminAuthController::class, 'updateProfile']);
        Route::post('create-admin', [AdminAuthController::class, 'createAdmin']);
        Route::post('create-teacher', [AdminAuthController::class, 'createTeacher']);
        Route::post('create-student', [AdminAuthController::class, 'createStudent']);

        Route::post('change-teacher-role', [AdminAuthController::class, 'changeTeacherRole']);
        Route::post('change-user-role', [AdminAuthController::class, 'changeUserRole']);


        // Global 
        Route::get('all-users', [AppController::class, 'allUsers']);
        Route::get('all-admins', [AppController::class, 'allAdmins']);
        Route::get('all-teachers', [AppController::class, 'allTeachers']);
        Route::get('all-group-users/{id}', [AppController::class, 'allGroupUsers']);

        // Batches
        Route::get('batches', [BatchController::class, 'indexAdmin']);
        Route::get('all-batches', [BatchController::class, 'indexAll']);
        Route::get('batches/{id}', [BatchController::class, 'show']);
        Route::post('batches/create', [BatchController::class, 'store']);
        Route::post('batches/update/{id}', [BatchController::class, 'update']);
        Route::post('batches/delete/{id}', [BatchController::class, 'destroy']);

        Route::get('batches/{id}/members', [BatchController::class, 'batchMembers']);
        Route::get('batches/{id}/users', [BatchController::class, 'batchUsers']);


        // Groups
        Route::get('groups', [GroupController::class, 'indexAdmin']);
        Route::get('all-groups', [GroupController::class, 'indexAll']);
        Route::get('groups/{id}', [GroupController::class, 'show']);
        Route::post('groups/create', [GroupController::class, 'store']);
        Route::post('groups/update/{id}', [GroupController::class, 'update']);
        Route::post('groups/delete/{id}', [GroupController::class, 'destroy']);
        Route::post('groups/add-member', [GroupController::class, 'addMember']);
        Route::post('groups/remove-member', [GroupController::class, 'removeMember']);

        // Group Wird Configs
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

        // // Group wirds
        // Route::get('group-wirds', [GroupWirdController::class, 'index']);
        // Route::get('group-wirds/{id}', [GroupWirdController::class, 'groupWirds']);

        ///
        /// New wirds
        ///
        // Group config
        Route::get('group-config/{groupId}', [GroupConfigController::class, 'show']);
        Route::post('update-group-config/{groupId}', [GroupConfigController::class, 'update']);
        // Group Wirds
        Route::get('group-today-wird/{groupId}', [GroupWirdController::class, 'show']);
        Route::post('add-today-wird/{test?}', [GroupWirdController::class, 'store'])
            ->where('test', '^(test)?$');

        // User Wirds Done
        // Route::post('make-wird-done', [GroupWirdController::class, 'userDoneWird']);


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
        Route::post('/create-notification', [NotificationController::class, 'createNotification']);
    });
});