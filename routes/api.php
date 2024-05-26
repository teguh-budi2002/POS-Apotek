<?php

use App\Http\Controllers\Api\Auth\LoginApiController;
use App\Http\Controllers\Api\Auth\RegisterApiController;
use App\Http\Controllers\Api\ProductApiController;
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