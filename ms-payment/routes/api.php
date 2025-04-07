<?php

use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::prefix('ms-payment')->group(function () {
    Route::post('/',[PaymentController::class, 'makePayment']);
});
