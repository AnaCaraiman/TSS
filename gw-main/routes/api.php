<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::get('user', [AuthController::class, 'getUserInfo'])->name('user');
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::post('register', [AuthController::class, 'register'])->name('register');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('refresh', [AuthController::class, 'refresh'])->name('refresh');
    Route::delete('/', [AuthController::class, 'deleteAccount'])->name('delete');
});

Route::prefix('product')->group(function () {
    Route::get('/', [ProductController::class, 'getProducts']);
    Route::get('/{id}', [ProductController::class, 'getProduct']);
    Route::get('/category',[ProductController::class, 'getCategories']);
});

Route::prefix('catalog')->group(function () {
    Route::get('/', [CatalogController::class, 'getCatalog']);
});

Route::prefix('cart')->group(function () {
    Route::get('/', [CartController::class, 'getCart']);
    Route::post('/', [CartController::class, 'addToCart']);
    Route::put('/', [CartController::class, 'updateCart']);
    Route::delete('/', [CartController::class, 'clearCart']);
});

Route::prefix('order')->group(function () {
    Route::get('/', [OrderController::class, 'getOrders']);
    Route::post('/', [OrderController::class, 'addOrder']);
    Route::delete('/', [OrderController::class, 'deleteOrder']);
    Route::get('/{orderId}', [OrderController::class, 'getOrder']);
});

Route::prefix('payment')->group(function () {
    Route::post('/',[PaymentController::class,'makePayment']);
});




