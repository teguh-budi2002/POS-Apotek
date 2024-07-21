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
    private $getRelation;

    public function __construct()
    {
        $this->getRelation = [
                'category' => function($q) {
                    $q->select("id", "name");
                },
                'unit' => function($q) {
                    $q->select("id", "name");
                },
                'stock' => function($q) {
                    $q->select("id", "product_id", "stock", "minimum_stock_level", "maximum_stock_level");
                }
            ];
    }

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
            $productCreated = Product::create($validation);
            $productId = $productCreated->id;

            $addedProduct = Product::with($this->getRelation)->findOrFail($productId);
            DB::commit();
            
            return $this->responseJson($addedProduct, 201, "Produk Berhasil Ditambah");
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->responseJson(500, $th->getMessage());
        }
    }

    public function getProducts(Request $request) {
        $perPage = $request->get('rows', 10);
        $page = $request->get('page', 1);
        $query = Product::with($this->getRelation);

        if ($request->has('search')) {
            $query->where('product_code', 'like', '%' . $request->search . '%')
                  ->orWhere('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->paginate($perPage, ['*'], 'page', $page);

        if ($products->isNotEmpty()) {
            return $this->responseJson([
                'products' => $products, 
                'total' => $products->total()
            ], 200, "Berhasil Mengambil Daftar Produk");
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
            
            $newUpdatedData = Product::with($this->getRelation)->whereId($product->id)->firstOrFail();

            return $this->responseJson(['newUpdatedProduct' => $newUpdatedData], 200, "Produk Berhasil Diedit");
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
            DB::rollBack();
            return $this->responseJson(500, $th->getMessage());
        }
    }
}
