<?php

use App\Http\Controllers\RecommendationController;
use Illuminate\Support\Facades\Route;

Route::prefix('recommendation')->group(function () {
    Route::get('/', [RecommendationController::class, 'getActions']);
   Route::get('/{id}',[RecommendationController::class, 'getMostPopularProducts'])->name('getRecommendations');
   Route::get('/personalised/{id}',[RecommendationController::class, 'getPersonalisedProducts'])->name('getPersonalisedProducts');
   Route::post('/',[RecommendationController::class, 'addUserAction'])->name('addUserAction');
});
