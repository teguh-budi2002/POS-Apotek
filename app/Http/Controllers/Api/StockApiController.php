<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StockRequest;
use App\Models\StockProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockApiController extends Controller
{
    private $query;
    
    public function __construct()
    {
        $this->query = StockProduct::select(
            "id",
            "product_id",
            "stock",
            "minimum_stock_level",
            "maximum_stock_level",
        )->newQuery();   
    }

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
        $stockProducts = $this->query->get();

        return $stockProducts->isNotEmpty()
            ? $this->responseJson($stockProducts, 200, "Berhasil Mengambil Daftar Stock Produk")
            : $this->responseJson(404, "Tidak Ada Daftar Stock Produk");
    }

     public function getPaginateStocks(Request $request) {
        $perPage = $request->get('rows', 10);
        $page = $request->get('page', 1);
        
        $this->query->scopeFilterStockByProduct($request->search);

        $stocks = $this->query->with('product')->paginate($perPage, ['*'], 'page', $page);

        return $stocks->isNotEmpty()
            ? $this->responseJson(['stocks' => $stocks, 'total' => $stocks->total()], 200, "Berhasil Mengambil Daftar Paginasi Satuan Produk")
            : $this->responseJson(404, "Tidak Ada Daftar Paginasi Satuan Produk");
    }

    public function getTrashedStocks() {
        $trashedStock = $this->query->onlyTrashed()->get();

        return $trashedStock->isNotEmpty()
            ? $this->responseJson($trashedStock, 200, "Berhasil Mengambil Daftar Stock (Trashed)")
            : $this->responseJson(404, "Tidak Ada Daftar Stock (Trashed)");
    }

    public function restoreTrashedStock($stockId) {
        $stock = StockProduct::onlyTrashed()->findOrFail($stockId);
        $stock->restore();

        $restoredStock = $this->query->findOrFail($stockId);

        return $this->responseJson([
            'restoredStock' => $restoredStock
        ], 200, "Berhasil Restore Data Stock");
    }

    public function editStock(StockRequest $request, $stockId) {
        DB::beginTransaction();
        try {
            $validation = $request->validated();
            StockProduct::whereId($stockId)->update($validation);
            DB::commit();

            $updatedStockProduct = $this->query->with('product')->findOrFail($stockId);
            
            return $this->responseJson([
                'newUpdatedStockProduct' => $updatedStockProduct
            ], 200, "Stock Produk Berhasil Diedit");
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->responseJson(500, $th->getMessage());
        }
    }

    public function deleteStock($stockId) {
        DB::beginTransaction();
        try {
            StockProduct::whereId($stockId)->delete();
            DB::commit();

            $trashedStock = $this->query->onlyTrashed()->findOrFail($stockId);

            return $this->responseJson([
                'trashedStock' => $trashedStock
            ], 200, "Stock Produk Berhasil Dihapus");
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->responseJson(500, $th->getMessage());
        }
    }
}
