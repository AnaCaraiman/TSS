<?php

use App\Http\Controllers\AuthController;
use App\Mail\WelcomeMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::post('register', [AuthController::class, 'register'])->name('register');
    Route::get('users',[AuthController::class,'getAllUsers'])->name('users');

    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('refresh', [AuthController::class, 'refresh'])->name('refresh');
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('user', [AuthController::class, 'getUserId'])->name('user');
        Route::delete('', [AuthController::class, 'deleteAccount'])->name('delete');
        Route::get('userInfo', [AuthController::class, 'getUser'])->name('userInfo');
        Route::post('profilePicture', [AuthController::class, 'uploadProfilePicture'])->name('profilePicture');
        Route::put('',[AuthController::class,'changePassword'])->name('changePassword');


    });

    Route::get('/test-mail', function () {
        $user = User::first();
        Mail::to('your@mailtrap.io')->send(new WelcomeMail($user));
        return 'Mail sent!';
    });
});
