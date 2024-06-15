<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PurchaseProductRequest;
use App\Models\Product;
use App\Models\PurchaseProduct;
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
