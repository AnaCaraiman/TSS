<?php

use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\FavoriteItemController;
use Illuminate\Support\Facades\Route;

Route::prefix('favorite')->group(function () {
    Route::get('/{id}', [FavoriteController::class, 'getFavoriteCart']);

    Route::prefix('item')->group(function () {
        Route::post('/', [FavoriteItemController::class, 'addFavoriteItem']);
        Route::delete('/', [FavoriteItemController::class, 'removeFavoriteItem']);
    });
});
