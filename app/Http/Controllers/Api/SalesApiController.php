<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SalesProductRequest;
use App\Models\SalesProduct;
use App\Models\StockProduct;
use App\StatusOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesApiController extends Controller
{
      public function salesProduct(SalesProductRequest $request) {
        DB::beginTransaction();
        try {
            $validation = $request->validated();
            $validation['status_order'] = StatusOrder::Pending;
            $validation['payment_method'] = $request->payment_method;
            $validation['status_payment'] = $request->status_payment;
            $order = SalesProduct::create($validation);

            // Handle stock < request qty put on FrontEnd
            $existingStockByOrderedProduct = StockProduct::select("id", "stock")
                                                         ->whereIn('product_id', $request->product_id)
                                                         ->get();
            foreach ($existingStockByOrderedProduct as $key => $currentStock) {
                $currentStock->stock -= $request->qty[$key];
                $currentStock->save();
            }

            $combineProductAndQty = [];
            foreach ($request->product_id as $key => $productId) {
                $combineProductAndQty[$productId] = ['qty' => $request->qty[$key]];
            }
            $order->salesProduct()->attach($combineProductAndQty);

            DB::commit();
            return $this->responseJson(201, "Penjualan produk kepada customer berhasil");
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->responseJson(500, $th->getMessage());
        }
    }

    public function getAllSalesProduct(){
        $salesProduct = SalesProduct::withWhereHas('salesProduct')->get();
        if ($salesProduct->isNotEmpty()) {
            return $this->responseJson($salesProduct, 200, "Berhasil mengambil daftar penjualan produk");
        }

        return $this->responseJson(404, "Tidak Ada Daftar Penjualan Produk");
    }
}
