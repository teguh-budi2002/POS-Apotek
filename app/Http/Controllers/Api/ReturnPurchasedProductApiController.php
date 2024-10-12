<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReturnPurchaseProductRequest;
use App\Models\ReturnPurchasedProduct;
use App\Models\StockProduct;
use App\StatusReturn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReturnPurchasedProductApiController extends Controller
{
    private $getRelation;

    public function __construct() {
     $this->getRelation = [
        'returnDetails' => function($query) {
            $query->select('purchase_products.id', 'purchase_products.reference_number');
        },
        'returnDetails.purchasedProductsWithoutDetail' => function($query) {
            $query->select('products.id', 'products.name', 'products.product_code', 'products.img_product');
        },
        'apotek' => function($query) {
            $query->select('apoteks.id', 'apoteks.name_of_apotek');
        },
        'supplier' => function($query) {
            $query->select('suppliers.id', 'suppliers.supplier_name');
        }
     ];   
    }
    
    public function addReturnPurchaseProduct(ReturnPurchaseProductRequest $request) {
        DB::beginTransaction();
        try {
            $validation = $request->validated();
            $validation['action_by'] = Auth::user()->name;
            $product_ids = $request->product_ids;
            $updatedStock = self::updateStock($validation['return_status'], $product_ids, $request->newStocks);

            $return_products = ReturnPurchasedProduct::create($validation);
            DB::commit();

            $return_products->returnDetails()->attach($request->ref_numbers);
      
            return $this->responseJson(200, 'Return pembelian produk berhasil ditambahkan');
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->responseJson(500, $th->getMessage());
        }
    }

    public function editReturnPurchaseProduct(ReturnPurchaseProductRequest $request, $return_ref_num) {
        DB::beginTransaction();
        try {
            $validation = $request->validated();
            $validation['action_by'] = Auth::user()->name;
            $product_ids = $request->product_ids;
            $updatedStock = self::updateStock($validation['return_status'], $product_ids, $request->newStocks);

            $return_product = ReturnPurchasedProduct::where('return_reference_number', $return_ref_num)->first();
            $return_product->update($validation);
            DB::commit();

            $return_product->returnDetails()->sync($request->ref_numbers);
      
            return $this->responseJson([
                'editedReturnPurchasedProduct' => $return_product
            ], 200, 'Return pembelian produk berhasil diubah');
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->responseJson(500, $th->getMessage());
        }
    }

    public function getPaginateReturnPurchasedProduct(Request $request){
        $perPage = 10;
        $purchasedProducts = ReturnPurchasedProduct::with($this->getRelation);
        
        if ($request->has('filters')) {
            $purchasedProducts->filterReturnPurchasedProducts($request->filters);
        }
       
        $purchasedProducts = $purchasedProducts->cursorPaginate($perPage);

        return $purchasedProducts->isNotEmpty()
            ? $this->responseJson(['returnPurchasedProducts' => $purchasedProducts], 200, "Berhasil mengambil daftar pengembalian produk")
            : $this->responseJson(404, "Tidak Ada Daftar Pengembalian Produk");
    }

    private function updateStock($status, $product_ids, $new_stocks) {
        if ($status === StatusReturn::COMPLETED) {
            $currentStock = StockProduct::select("id", "stock", "product_id")->whereIn('product_id', $product_ids)->get();
            foreach ($currentStock as $stock) {
                $stock->stock -= $new_stocks[$stock->product_id]->stock;
                $stock->save();
            }
        }
    }
}
