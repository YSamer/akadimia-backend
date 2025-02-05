<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BatchController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\UserAuthController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AppController;
use App\Http\Controllers\BatchApplyController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\GroupConfigController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\TeacherAuthController;
// use App\Http\Controllers\GroupWirdConfigController;
// use App\Http\Controllers\GroupWirdController;
// use App\Http\Controllers\WirdController;
// use App\Http\Controllers\WirdDoneController;

// User Auth 
Route::get('admin/verify/{id}/{hash}', [AdminAuthController::class, 'verify'])->name('admin.verification.verify');
Route::get('/email/verify/{id}/{hash}', [UserAuthController::class, 'verify'])->name('user.verification.verify');

Route::prefix('v1')->group(function () {
    require_once 'apps/admin_api.php';
    require_once 'apps/teacher_api.php';
    require_once 'apps/user_api.php';
});