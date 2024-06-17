<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StockRequest;
use App\Models\StockProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockApiController extends Controller
{
    public function addStock(StockRequest $request) {
        DB::beginTransaction();
        try {
           $validation = $request->validated();
           StockProduct::create($validation);
           DB::commit();
           
           return $this->responseJson(201, "Penambahan Stock Pada Produk Berhasil");
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->responseJson(500, $th->getMessage());
        }
    }

    public function getAllStock() {
        $products = StockProduct::with('product')->get();
        if ($products->isNotEmpty()) {
            return $this->responseJson($products, 200, "Berhasil Mengambil Daftar Stock Produk");
        }

        return $this->responseJson(404, "Tidak Ada Daftar Stock Produk");
    }

    public function editStock(StockRequest $request, $stockId) {
        DB::beginTransaction();
        try {
            $validation = $request->validated();
            StockProduct::whereId($stockId)->update($validation);
            DB::commit();
            
            return $this->responseJson(201, "Stock Produk Berhasil Diedit");
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->responseJson(500, $th->getMessage());
        }
    }

    public function deleteStock($stockId) {
        DB::beginTransaction();
        try {
            $deletedStockProduct = StockProduct::whereId($stockId)->delete();
            DB::commit();

            return $this->responseJson(200, "Stock Produk Berhasil Dihapus");
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->responseJson(500, $th->getMessage());
        }
    }
}
