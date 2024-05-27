<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductApiController extends Controller
{
    private $img_name = '';

    public function addProduct(ProductRequest $request) {
        DB::beginTransaction();
        try {
            $validation = $request->validated();

            if ($request->file('img_product')) {
                $file  = $request->file('img_product');
                $this->img_name = $file->getClientOriginalName();
                $storeImage = Storage::putFileAs('/public/product', $file, $this->img_name);
                $validation['img_product'] = $this->img_name;
            }
            Product::create($validation);
            DB::commit();
            
            return $this->responseJson(201, "Produk Berhasil Ditambah");
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->responseJson(500, $th->getMessage());
        }
    }

    public function getProducts() {
        $products = Product::get();
        if ($products->isNotEmpty()) {
            return $this->responseJson($products, 200, "Berhasil Mengambil Daftar Produk");
        }

        return $this->responseJson(404, "Tidak Ada Daftar Produk");
    }

    public function editProduct(ProductRequest $request, Product $product) {
        DB::beginTransaction();
        try {
            $validation = $request->validated();
            
            if ($request->file('img_product')) {
                if (!is_null($product->img_product)) {
                    $deleteOldImage = Storage::delete('/public/product/' . $product->img_product);
                }

                $file  = $request->file('img_product');
                $this->img_name = $file->getClientOriginalName();
                $storeImage = Storage::putFileAs('/public/product', $file, $this->img_name);
                $validation['img_product'] = $this->img_name;
            }
            Product::whereId($product->id)->update($validation);
            DB::commit();
            
            return $this->responseJson(201, "Produk Berhasil Diedit");
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->responseJson(500, $th->getMessage());
        }
    }

    public function deleteProduct($id) {
        DB::beginTransaction();
        try {
            $product = Product::whereId($id)->first();
            $deleteOldImage = Storage::delete('/public/product/' . $product->img_product);
            DB::commit();
            $product->delete();
    
            return $this->responseJson(200, "Product Berhasil Dihapus");
        } catch (\Throwable $th) {
            return $this->responseJson(500, $th->getMessage());
        }
    }
}
