<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PurchaseProductRequest;
use App\Models\Product;
use App\Models\PurchaseProduct;
use App\Models\StockProduct;
use App\StatusOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseApiController extends Controller
{
    public function purchasedProduct(PurchaseProductRequest $request) {
        DB::beginTransaction();
        try {
            $validation = $request->validated();
            $validation['status_order'] = StatusOrder::Pending;
            $validation['payment_method'] = $request->payment_method;
            $validation['status_payment'] = $request->status_payment;
            $order = PurchaseProduct::create($validation);

            // Sum stock & qty while order product
            $existingStockByOrderedProduct = StockProduct::whereIn('product_id', $request->product_id)
                                                          ->pluck('product_id')
                                                          ->toArray();
            $missingProduct = array_diff($request->product_id, $existingStockByOrderedProduct);

            if (!empty($missingProduct)) {
                $productMissingName = Product::whereIn('id', $missingProduct)->pluck('name')->toArray();
                $productMissingNameStr = implode(", ", $productMissingName);

                return $this->responseJson([
                        'missing_products' => $productMissingName,
                        'missing_product_id' => $missingProduct
                    ],
                    400, 
                    "Harap sesuaikan stock awal terlebih dahulu pada product: $productMissingNameStr, agar meminimalisir kesalahan dalam inventaris.");
            } else {
                $currentStock = StockProduct::select("id", "stock")->whereIn('product_id', $existingStockByOrderedProduct)->get();
                foreach ($currentStock as $key => $stock) {
                    $stock->stock += $request->qty[$key];
                    $stock->save();
                }
            }

            $combineProductAndQty = [];
            foreach ($request->product_id as $key => $productId) {
                $combineProductAndQty[$productId] = ['qty' => $request->qty[$key]];
            }
            $order->purchasedProducts()->attach($combineProductAndQty);

            DB::commit();
            return $this->responseJson(201, "Pembelian produk kepada supplier berhasil");
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->responseJson(500, $th->getMessage());
        }
    }

    public function getAllPurchasedProduct(){
        $purchasedProducts = PurchaseProduct::withWhereHas('purchasedProducts')->get();
        if ($purchasedProducts->isNotEmpty()) {
            return $this->responseJson($purchasedProducts, 200, "Berhasil mengambil daftar order produk");
        }

        return $this->responseJson(404, "Tidak Ada Daftar Order Produk");
    }
}
