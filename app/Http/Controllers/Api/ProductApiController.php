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
    private $query;

    public function __construct()
    {
        $this->query = Product::select(
            'id',
            'category_product_id',
            'unit_product_id',
            'product_code',
            'name',
            'unit_price',
            'profit_margin',
            'unit_selling_price',
            'description',
            'img_product',
            'isActive'  
        )->with([
            'category' => function($q) {
                $q->select("id", "name", "isActive")->isActive();
            },
            'unit' => function($q) {
                $q->select("id", "name", "isActive")->isActive();
            },
            'stock' => function($q) {
                $q->select("id", "product_id", "stock", "minimum_stock_level", "maximum_stock_level");
            }
        ])
        ->newQuery();
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
            
            if ($request->has('stock') || $request->has('minimum_stock_level')) {
                $productCreated->stock()->create([
                   'stock' => $request->stock,
                   'minimum_stock_level' => $request->minimum_stock_level, 
                   'maximum_stock_level' => $request->maximum_stock_level, 
                ]);    
            }
            
            $productId = $productCreated->id;
            $addedProduct = $this->query->findOrFail($productId);
            DB::commit();
            
            return $this->responseJson($addedProduct, 201, "Produk Berhasil Ditambah");
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->responseJson(500, $th->getMessage());
        }
    }

    public function getAllProducts() {
        $products = $this->query->get();

        return $products->isNotEmpty()
            ? $this->responseJson($products, 200, "Berhasil Mengambil Daftar Produk")
            : $this->responseJson(404, "Tidak Ada Daftar Produk");
    }

    public function getListProductBySpecificColumn(Request $request) {
        $searchQuery = $request->search;
        $columns = $request->customColumn;
        array_unshift($columns, "id");

        if (!is_null($searchQuery)) {
            $products = Product::with(['unit' => function($q) {
                                    $q->select("id", "name")->isActive();
                                }])
                                ->select($columns)
                                ->isActive()
                                ->filterProductWithoutDescription($request->search)
                                ->orderByDesc("name")
                                ->take(10)
                                ->get();
    
            return $products->isNotEmpty()
                ? $this->responseJson($products, 200, "Berhasil Mengambil Daftar Produk by Filtered Purchased Product")
                : $this->responseJson(404, "Tidak Ada Daftar Produk");
        } else {
            return $this->responseJson(200, "Silahkan Masukkan Kata Kunci");
        }
    }

    public function getProductsPerPage(Request $request) {
        $perPage = $request->get('rows', 10);
        $page = $request->get('page', 1);
    
        $this->query->filterProduct($request->search);

        $products = $this->query->paginate($perPage, ['*'], 'page', $page);

        return $products->isNotEmpty() 
                ? $this->responseJson(['products' => $products, 'total' => $products->total()], 200, "Berhasil Mengambil Daftar Produk") 
                : $this->responseJson(404, "Tidak Ada Daftar Produk");
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
            
            $newUpdatedData = $this->query->findOrFail($product->id);

            return $this->responseJson(['newUpdatedProduct' => $newUpdatedData], 200, "Produk Berhasil Diedit");
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->responseJson(500, $th->getMessage());
        }
    }

    public function setStatusProduct(Request $request, $productId) {
        DB::beginTransaction();
        try {
            Product::whereId($productId)->update([
                'isActive' => $request->status
            ]);
            DB::commit();

            $newUpdatedData = $this->query->findOrFail($productId);

            return $this->responseJson([
                'newUpdatedProduct' => $newUpdatedData
            ],200, "Status Product Berhasil Diedit");
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->responseJson(500, $th->getMessage());
        }
    }

    public function deleteProduct($id) {
        DB::beginTransaction();
        try {
            $product = Product::findOrFail($id);
            $deleteOldImage = Storage::delete('/public/product/' . $product->img_product);
            $product->delete();
            DB::commit();
    
            return $this->responseJson(200, "Product Berhasil Dihapus");
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->responseJson(500, $th->getMessage());
        }
    }
}
