<?php

use App\Http\Controllers\FavoriteController;
use Illuminate\Support\Facades\Route;

Route::prefix('favorite')->group(function () {
    Route::get('/{id}', [FavoriteController::class, 'getFavoriteCart']);
});
