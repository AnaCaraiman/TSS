<?php

use App\Http\Controllers\CatalogController;
use Illuminate\Support\Facades\Route;

Route::prefix('ms-catalog')->group(function () {
    Route::get('/',[CatalogController::class,'getCatalog']);
});


