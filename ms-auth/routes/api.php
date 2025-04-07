<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::post('register', [AuthController::class, 'register'])->name('register');

    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('refresh-token', [AuthController::class, 'refresh'])->name('refresh');
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('user', [AuthController::class, 'getUserId'])->name('user');
        Route::delete('', [AuthController::class, 'deleteAccount'])->name('delete');
        Route::get('userInfo', [AuthController::class, 'getUser'])->name('userInfo');


    });
});
