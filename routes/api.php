<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BatchController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\UserAuthController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AppController;
use App\Http\Controllers\BatchApplyController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\GroupWirdConfigController;
use App\Http\Controllers\TeacherAuthController;
use App\Http\Controllers\WirdController;
use App\Http\Controllers\WirdDoneController;

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

        // Wirds
        Route::get('today-wirds/{id}', [WirdController::class, 'groupTodayWirdsStudent']);
        Route::post('wird-done', [WirdDoneController::class, 'wirdDone']);
        Route::post('wird-not-done', [WirdDoneController::class, 'wirdDone']);

        // Exams
        Route::get('/get-exams', [ExamController::class, 'getUserExams']);
        Route::get('/get-exam/{id}', [ExamController::class, 'getExamDetails']);
        Route::post('/send-responses', [ExamController::class, 'submitResponse']);
    });
});
Route::prefix('admin')->group(function () {
    Route::post('register', [AdminAuthController::class, 'register']);
    Route::post('login', [AdminAuthController::class, 'login']);
    Route::post('re-send-otp', [AdminAuthController::class, 'reSendOtp']);
    Route::post('forgot-password', [AdminAuthController::class, 'forgotPassword']);
    Route::post('verify-otp-reset', [AdminAuthController::class, 'verifyOtpForPasswordReset']);
    Route::post('reset-password', [AdminAuthController::class, 'resetPassword']);
    Route::post('verify-otp', [AdminAuthController::class, 'verifyOtp']);

    Route::middleware('auth:admin')->group(function () {
        Route::get('auto-login', [AdminAuthController::class, 'autoLogin']);
        Route::get('logout', [AdminAuthController::class, 'logout']);
        Route::post('update-profile', [AdminAuthController::class, 'updateProfile']);

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
        Route::get('group-wird-configs', [GroupWirdConfigController::class, 'index']);
        Route::get('group-wird-configs/{id}', [GroupWirdConfigController::class, 'show']);
        Route::post('group-wird-configs/create', [GroupWirdConfigController::class, 'store']);
        Route::post('group-wird-configs/update/{id}', [GroupWirdConfigController::class, 'update']);
        Route::post('group-wird-configs/delete/{id}', [GroupWirdConfigController::class, 'destroy']);

        // Wird 
        Route::post('set-today-wird', [WirdController::class, 'setTodayWirds']);
        Route::get('wirds', [WirdController::class, 'index']);
        Route::get('today-wirds', [WirdController::class, 'todayWirds']);
        Route::get('today-wirds/{id}', [WirdController::class, 'groupTodayWirds']);

        // Exams
        Route::get('/get-exams', [ExamController::class, 'getExams']);
        Route::get('/get-exam/{id}', [ExamController::class, 'getExamDetails']);
        Route::post('/create-exam', [ExamController::class, 'createExam']);
        Route::post('/add-questions', [ExamController::class, 'addQuestions']);
        Route::post('/delete-question/{id}', [ExamController::class, 'deleteQuestion']);
        Route::get('/view-responses/{examId}', [ExamController::class, 'viewResponses']);


    });
});

Route::prefix('teacher')->group(function () {
    Route::post('register', [TeacherAuthController::class, 'register']);
    Route::post('login', [TeacherAuthController::class, 'login']);
    Route::post('re-send-otp', [TeacherAuthController::class, 'reSendOtp']);
    Route::post('forgot-password', [TeacherAuthController::class, 'forgotPassword']);
    Route::post('verify-otp-reset', [TeacherAuthController::class, 'verifyOtpForPasswordReset']);
    Route::post('reset-password', [TeacherAuthController::class, 'resetPassword']);
    Route::post('verify-otp', [TeacherAuthController::class, 'verifyOtp']);

    Route::middleware('auth:teacher')->group(function () {
        Route::get('auto-login', [TeacherAuthController::class, 'autoLogin']);
        Route::get('logout', [TeacherAuthController::class, 'logout']);
        Route::post('update-profile', [TeacherAuthController::class, 'updateProfile']);

        // Batches
        Route::get('batches', [BatchController::class, 'index']);
        Route::get('batches/{id}', [BatchController::class, 'show']);
        // Route::post('batches/create', [BatchController::class, 'store']);
        // Route::post('batches/update/{id}', [BatchController::class, 'update']);
        // Route::post('batches/delete/{id}', [BatchController::class, 'destroy']);

        // Groups
        Route::get('groups', [GroupController::class, 'index']);
        Route::get('groups/{id}', [GroupController::class, 'show']);
        // Route::post('groups/create', [GroupController::class, 'store']);
        // Route::post('groups/update/{id}', [GroupController::class, 'update']);
        // Route::post('groups/delete/{id}', [GroupController::class, 'destroy']);

        // Group Configs
        // Route::get('group-wird-configs', [GroupWirdConfigController::class, 'index']);
        // Route::get('group-wird-configs/{id}', [GroupWirdConfigController::class, 'show']);
        // Route::post('group-wird-configs/create', [GroupWirdConfigController::class, 'store']);
        // Route::post('group-wird-configs/update/{id}', [GroupWirdConfigController::class, 'update']);
        // Route::post('group-wird-configs/delete/{id}', [GroupWirdConfigController::class, 'destroy']);
    });
});
