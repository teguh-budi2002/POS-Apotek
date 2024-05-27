<?php

use App\Http\Controllers\Api\Auth\LoginApiController;
use App\Http\Controllers\Api\Auth\RegisterApiController;
use App\Http\Controllers\Api\CategoryProductApiController;
use App\Http\Controllers\Api\ProductApiController;
use App\Http\Controllers\Api\UnitProductApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('register/process',[RegisterApiController::class, 'registerProcess']);
Route::post('login/process',[LoginApiController::class, 'loginProcess']);

Route::prefix('product')->middleware('auth:sanctum')->group(function() {
  Route::get('/', [ProductApiController::class, 'getProducts']);
  Route::post('add-product',[ProductApiController::class, 'addProduct']);
  Route::patch('edit-product/{product:id}',[ProductApiController::class, 'editProduct']);
  Route::delete('delete-product/{id}',[ProductApiController::class, 'deleteProduct']);
});

Route::prefix('category')->middleware('auth:sanctum')->group(function() {
  Route::get('/', [CategoryProductApiController::class, 'getCategories']);
  Route::post('add-category', [CategoryProductApiController::class, 'createCategory']);
  Route::patch('edit-category/{id}', [CategoryProductApiController::class, 'editCategory']);
  Route::delete('delete-category/{id}', [CategoryProductApiController::class, 'deleteCategory']);
});

Route::prefix('unit')->middleware('auth:sanctum')->group(function() {
  Route::get('/', [UnitProductApiController::class, 'getUnits']);
  Route::post('add-unit', [UnitProductApiController::class, 'createUnit']);
  Route::patch('edit-unit/{id}', [UnitProductApiController::class, 'editUnit']);
  Route::delete('delete-unit/{id}', [UnitProductApiController::class, 'deleteUnit']);
});