<?php

use App\Http\Controllers\Api\ApotekApiController;
use App\Http\Controllers\Api\Auth\LoginApiController;
use App\Http\Controllers\Api\Auth\RegisterApiController;
use App\Http\Controllers\Api\CategoryProductApiController;
use App\Http\Controllers\Api\CustomerApiController;
use App\Http\Controllers\Api\ProductApiController;
use App\Http\Controllers\Api\PurchaseApiController;
use App\Http\Controllers\Api\SalesApiController;
use App\Http\Controllers\Api\StockApiController;
use App\Http\Controllers\Api\SupplierApiController;
use App\Http\Controllers\Api\UnitProductApiController;
use Illuminate\Support\Facades\Route;

Route::post('register/process',[RegisterApiController::class, 'registerProcess']);
Route::post('login/process',[LoginApiController::class, 'loginProcess']);

Route::prefix('apotek')->middleware('auth:sanctum')->group(function() {
  Route::get('/', [ApotekApiController::class, 'getAllApotek']);
  Route::post('add-apotek',[ApotekApiController::class, 'addApotek']);
  Route::patch('edit-apotek/{id}',[ApotekApiController::class, 'editApotek']);
  Route::delete('delete-apotek/{id}',[ApotekApiController::class, 'deleteApotek']);
});

Route::prefix('supplier')->middleware('auth:sanctum')->group(function() {
  Route::get('/', [SupplierApiController::class, 'getAllSupplier']);
  Route::post('add-supplier',[SupplierApiController::class, 'addSupplier']);
  Route::patch('edit-supplier/{id}',[SupplierApiController::class, 'editSupplier']);
  Route::delete('delete-supplier/{id}',[SupplierApiController::class, 'deleteSupplier']);
});

Route::prefix('customer')->middleware('auth:sanctum')->group(function() {
  Route::get('/', [CustomerApiController::class, 'getAllCustomer']);
  Route::post('add-customer',[CustomerApiController::class, 'addCustomer']);
  Route::patch('edit-customer/{id}',[CustomerApiController::class, 'editCustomer']);
  Route::delete('delete-customer/{id}',[CustomerApiController::class, 'deleteCustomer']);
});

Route::prefix('product')->middleware('auth:sanctum')->group(function() {
  Route::get('/', [ProductApiController::class, 'getProducts']);
  Route::post('add-product',[ProductApiController::class, 'addProduct']);
  Route::patch('edit-product/{product:id}',[ProductApiController::class, 'editProduct']);
  Route::patch('set-status/{id}', [ProductApiController::class, 'setStatusProduct']);
  Route::delete('delete-product/{id}',[ProductApiController::class, 'deleteProduct']);
});

Route::prefix('category')->middleware('auth:sanctum')->group(function() {
  Route::get('/', [CategoryProductApiController::class, 'getCategories']);
  Route::get('/pagination', [CategoryProductApiController::class, 'getPaginateCategories']);
  Route::get('/trashed', [CategoryProductApiController::class, 'getTrashedCategories']);
  Route::post('restore-category/{id}', [CategoryProductApiController::class, 'restoreTrashedCategory']);
  Route::post('add-category', [CategoryProductApiController::class, 'createCategory']);
  Route::patch('edit-category/{id}', [CategoryProductApiController::class, 'editCategory']);
  Route::patch('set-status/{id}', [CategoryProductApiController::class, 'setStatusCategory']);
  Route::delete('delete-category/{id}', [CategoryProductApiController::class, 'deleteCategory']);
});

Route::prefix('unit')->middleware('auth:sanctum')->group(function() {
  Route::get('/', [UnitProductApiController::class, 'getUnits']);
  Route::get('/pagination', [UnitProductApiController::class, 'getPaginateUnits']);
  Route::get('/trashed', [UnitProductApiController::class, 'getTrashedUnit']);
  Route::post('restore-unit/{id}', [UnitProductApiController::class, 'restoreTrashedUnits']);
  Route::post('add-unit', [UnitProductApiController::class, 'createUnit']);
  Route::patch('edit-unit/{id}', [UnitProductApiController::class, 'editUnit']);
  Route::patch('set-status/{id}', [UnitProductApiController::class, 'setStatusUnit']);
  Route::delete('delete-unit/{id}', [UnitProductApiController::class, 'deleteUnit']);
});

Route::prefix('stock')->middleware('auth:sanctum')->group(function() {
  Route::get('/', [StockApiController::class, 'getAllStock']);
  Route::post('add-stock', [StockApiController::class, 'addStock']);
  Route::patch('edit-stock/{id}', [StockApiController::class, 'editStock']);
  Route::delete('delete-stock/{id}', [StockApiController::class, 'deleteStock']);
});

Route::prefix('ordered-product')->middleware('auth:sanctum')->group(function() {
  // Ordered Purchased Product into Supplier
  Route::post('purchase-product', [PurchaseApiController::class, 'purchasedProduct']);
  Route::get('get-all-purchased-product', [PurchaseApiController::class, 'getAllPurchasedProduct']);

  // Ordered Sales Product Into Customer
  Route::post('sales-product', [SalesApiController::class, 'salesProduct']);
  Route::get('get-all-sales-product', [SalesApiController::class, 'getAllSalesProduct']);
});