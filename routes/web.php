<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\TeacherAuthController;
use App\Http\Controllers\UserAuthController;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

// Route::get('/email/verify', function () {
//     return view('auth.verify');
// })->middleware('auth')->name('verification.notice');

// Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $r) {
//     $r->fulfill();

//     return redirect('/home');
// })->middleware(['auth', 'signed'])->name('verification.verify');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/send-admin-notification/{admin}', [AdminAuthController::class, 'notify']);
Route::get('/send-teacher-notification/{teacher}', [TeacherAuthController::class, 'notify']);
Route::get('/send-user-notification/{user}', [UserAuthController::class, 'notify']);