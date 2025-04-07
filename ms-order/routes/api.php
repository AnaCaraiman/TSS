<?php

use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::prefix('ms-order')->group(function () {
    Route::post('/', [OrderController::class, 'makeOrder']);
    Route::get('/',[OrderController::class, 'getOrders']);
    Route::get('/{orderId}',[OrderController::class, 'getOrder']);
    Route::delete('/{orderId}',[OrderController::class, 'cancelOrder']);
});

Route::get('/',function (){
    return view('welcome');
});
