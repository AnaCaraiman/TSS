<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductAttributeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductImageController;
use Illuminate\Support\Facades\Route;

Route::prefix('ms-product')->group(function () {
   Route::prefix('category')->group(function () {
       Route::post('/', [CategoryController::class, 'createCategory']);
       Route::get('/', [CategoryController::class, 'getCategories']);
       Route::get('/{id}', [CategoryController::class, 'getCategory']);
       Route::delete('/{id}', [CategoryController::class, 'deleteCategory']);

   });
   Route::prefix('attribute')->group(function () {
       Route::post('/',[ProductAttributeController::class,'createAttribute']);
       Route::delete('/',[ProductAttributeController::class,'deleteAttribute']);
       Route::get('/{id}',[ProductAttributeController::class,'getAttributes']);
   });

   Route::prefix('image')->group(function () {
       Route::post('/',[ProductImageController::class,'createImage']);
       Route::get('/{id}',[ProductImageController::class,'getImagesByProductId']);
       Route::delete('/{id}',[ProductImageController::class,'deleteImage']);
   });

   Route::post('/',[ProductController::class,'createProduct']);
   Route::get('/',[ProductController::class,'getProducts']);
   Route::get('/{id}',[ProductController::class,'getProduct']);
   Route::delete('/{id}',[ProductController::class,'removeProduct']);
});

