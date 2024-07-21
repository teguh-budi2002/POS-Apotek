<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryProductRequest;
use App\Models\CategoryProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryProductApiController extends Controller
{
    public function createCategory(CategoryProductRequest $request) {
        DB::beginTransaction();
        try {
            $validation = $request->validated();
            $createCategory = CategoryProduct::create($validation);
            $addedCategory = CategoryProduct::select("id", "name", "isActive")->findOrFail($createCategory->id);
            DB::commit();
            
            return $this->responseJson($addedCategory, 201, "Category Product Berhasil Dibuat");
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->responseJson(500, $th->getMessage());
        }
    }

    public function getCategories(Request $request) {
        $perPage = $request->get('rows', 10);
        $page = $request->get('page', 1);
        $query = CategoryProduct::query();

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $categories = $query->paginate($perPage, ['*'], 'page', $page);

        if ($categories->isNotEmpty()) {
            return $this->responseJson([
                'categories' => $categories, 
                'total' => $categories->total()
            ], 200, "Berhasil Mengambil Daftar Kategori Produk");
        }

        return $this->responseJson(404, "Tidak Ada Daftar Kategori Produk");
    }

    public function editCategory(CategoryProductRequest $request, $categoryId) {
        DB::beginTransaction();
        try {
            $validation = $request->validated();
            CategoryProduct::whereId($categoryId)->update($validation);
            DB::commit();

            $newUpdatedData = CategoryProduct::whereId($categoryId)->firstOrFail();

            return $this->responseJson([
                'newUpdatedCategory' => $newUpdatedData
            ],200, "Category Product Berhasil Diedit");
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->responseJson(500, $th->getMessage());
        }
    }

    public function setStatusCategory(Request $request, $categoryId) {
        DB::beginTransaction();
        try {
            CategoryProduct::whereId($categoryId)->update([
                'isActive' => $request->status
            ]);
            DB::commit();

            $newUpdatedData = CategoryProduct::whereId($categoryId)->firstOrFail();

            return $this->responseJson([
                'newUpdatedCategory' => $newUpdatedData
            ],200, "Status Category Product Berhasil Diedit");
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->responseJson(500, $th->getMessage());
        }
    }

    public function deleteCategory($categoryId) { 
        $doDelete = CategoryProduct::whereId($categoryId)->delete();
        return $this->responseJson(200, "Category Product Berhasil Dihapus");
    }
}
