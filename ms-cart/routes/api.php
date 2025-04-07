<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CartItemController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;

Route::get('/redis-test', function () {
    Cache::put('name', 'Laravel', 10);
    $value = Cache::get('name');
    return response()->json(['name' => $value]);
});

Route::prefix('ms-cart')->group(function () {
    Route::post('/', [CartController::class, 'createCart']);
    Route::put('/', [CartItemController::class, 'updateCart']);
    Route::get('/{id}', [CartController::class, 'getCart']);
    Route::delete('/{id}', [CartController::class, 'deleteCart']);

});

