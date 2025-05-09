<?php

use App\Http\Controllers\Api\ApotekApiController;
use App\Http\Controllers\Api\Auth\LoginApiController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Auth\RegisterApiController;
use App\Http\Controllers\Api\CategoryProductApiController;
use App\Http\Controllers\Api\CustomerApiController;
use App\Http\Controllers\Api\ProductApiController;
use App\Http\Controllers\Api\PurchaseApiController;
use App\Http\Controllers\Api\ReturnPurchasedProductApiController;
use App\Http\Controllers\Api\SalesApiController;
use App\Http\Controllers\Api\StockApiController;
use App\Http\Controllers\Api\SupplierApiController;
use App\Http\Controllers\Api\UnitProductApiController;
use App\Http\Controllers\Api\UserApiController;
use Illuminate\Support\Facades\Route;

Route::post('register/process',[RegisterApiController::class, 'registerProcess']);
Route::post('login/process',[LoginApiController::class, 'loginProcess']);
Route::post('logout',[LogoutController::class, 'logout']);

Route::prefix('user')->middleware('auth:sanctum')->group(function() {
  Route::get('get-name-of-users', [UserApiController::class, 'getNameOfUser']);
});

Route::prefix('apotek')->middleware('auth:sanctum')->group(function() {
  Route::get('/', [ApotekApiController::class, 'getAllApotek']);
  Route::get('/get-apotek/by-specifiec-column', [ApotekApiController::class, 'getApotekOnlySpecificColumn']);
  Route::get('/pagination', [ApotekApiController::class, 'getPaginateApoteks']);
  Route::get('/trashed', [ApotekApiController::class, 'getTrashedApoteks']);
  Route::post('restore-apotek/{id}', [ApotekApiController::class, 'restoreTrashedApotek']);
  Route::post('add-apotek',[ApotekApiController::class, 'addApotek']);
  Route::patch('edit-apotek/{id}',[ApotekApiController::class, 'editApotek']);
  Route::delete('delete-apotek/{id}',[ApotekApiController::class, 'deleteApotek']);
});

Route::prefix('supplier')->middleware('auth:sanctum')->group(function() {
  Route::get('/', [SupplierApiController::class, 'getAllSupplier']);
  Route::get('/get-supplier/by-specifiec-column', [SupplierApiController::class, 'getSupplierOnlySpecificColumn']);
  Route::get('/pagination', [SupplierApiController::class, 'getPaginateSuppliers']);
  Route::get('/trashed', [SupplierApiController::class, 'getTrashedSuppliers']);
  Route::post('restore-supplier/{id}', [SupplierApiController::class, 'restoreTrashedSupplier']);
  Route::post('add-supplier',[SupplierApiController::class, 'addSupplier']);
  Route::patch('edit-supplier/{id}',[SupplierApiController::class, 'editSupplier']);
  Route::delete('delete-supplier/{id}',[SupplierApiController::class, 'deleteSupplier']);
});

Route::prefix('customer')->middleware('auth:sanctum')->group(function() {
  Route::get('/', [CustomerApiController::class, 'getAllCustomer']);
  Route::get('/pagination', [CustomerApiController::class, 'getPaginateCustomers']);
  Route::get('/trashed', [CustomerApiController::class, 'getTrashedCustomers']);
  Route::post('restore-customer/{id}', [CustomerApiController::class, 'restoreTrashedCustomer']);
  Route::post('add-customer',[CustomerApiController::class, 'addCustomer']);
  Route::patch('edit-customer/{id}',[CustomerApiController::class, 'editCustomer']);
  Route::delete('delete-customer/{id}',[CustomerApiController::class, 'deleteCustomer']);
});

Route::prefix('product')->middleware('auth:sanctum')->group(function() {
  Route::get('/', [ProductApiController::class, 'getAllProducts']);
  Route::get('/get-list-product/by-specific-column', [ProductApiController::class, 'getListProductBySpecificColumn']);
  Route::get('/pagination', [ProductApiController::class, 'getProductsPerPage']);
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
  Route::get('/trashed', [UnitProductApiController::class, 'getTrashedUnits']);
  Route::post('restore-unit/{id}', [UnitProductApiController::class, 'restoreTrashedUnit']);
  Route::post('add-unit', [UnitProductApiController::class, 'createUnit']);
  Route::patch('edit-unit/{id}', [UnitProductApiController::class, 'editUnit']);
  Route::patch('set-status/{id}', [UnitProductApiController::class, 'setStatusUnit']);
  Route::delete('delete-unit/{id}', [UnitProductApiController::class, 'deleteUnit']);
});

Route::prefix('stock')->middleware('auth:sanctum')->group(function() {
  Route::get('/', [StockApiController::class, 'getAllStock']);
  Route::get('/pagination', [StockApiController::class, 'getPaginateStocks']);
  Route::get('/trashed', [StockApiController::class, 'getTrashedStocks']);
  Route::post('restore-stock/{id}', [StockApiController::class, 'restoreTrashedStock']);
  Route::post('add-stock', [StockApiController::class, 'addStock']);
  Route::patch('edit-stock/{id}', [StockApiController::class, 'editStock']);
  Route::delete('delete-stock/{id}', [StockApiController::class, 'deleteStock']);
});

Route::prefix('ordered-product')->middleware('auth:sanctum')->group(function() {
  // Ordered Purchased Product into Supplier
  Route::post('purchase-product', [PurchaseApiController::class, 'purchasedProduct']);
  Route::patch('edit-purchased-product/{purchase_product_order_id}', [PurchaseApiController::class, 'editPurchasedProduct']);
  Route::post('paid-order/{purchase_product_order_id}', [PurchaseApiController::class, 'paidOrder']);
  Route::post('change-status-order/{purchase_product_order_id}', [PurchaseApiController::class, 'changeStatusOrder']);

  Route::get('get-paginate-purchased-product', [PurchaseApiController::class, 'getPaginatePurchasedProduct']);
  Route::get('get-all-ref-numbers', [PurchaseApiController::class, 'getRefNumbers']);
  Route::post('list-product-for-return', [PurchaseApiController::class, 'getListOrderedProductForReturn']);

  // Ordered Sales Product Into Customer
  Route::post('sales-product', [SalesApiController::class, 'salesProduct']);
  Route::get('get-all-sales-product', [SalesApiController::class, 'getAllSalesProduct']);
});

Route::prefix('return-product')->middleware('auth:sanctum')->group(function() {
  Route::get('get-paginate-return-product', [ReturnPurchasedProductApiController::class, 'getPaginateReturnPurchasedProduct']);
  Route::post('add-return-product', [ReturnPurchasedProductApiController::class, 'addReturnPurchaseProduct']);
  Route::patch('edit-return-product/{return_ref_num}', [ReturnPurchasedProductApiController::class, 'editReturnPurchaseProduct']);
});