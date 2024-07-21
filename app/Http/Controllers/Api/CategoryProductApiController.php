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
            CategoryProduct::create($validation);
            DB::commit();
            
            return $this->responseJson(201, "Category Product Berhasil Dibuat");
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->responseJson(500, $th->getMessage());
        }
    }

    public function getCategories() {
        $categories = CategoryProduct::select("id", "name", "isActive")->get();
        if ($categories->isNotEmpty()) {
            return $this->responseJson($categories, 200, "Berhasil Mengambil Daftar Category Product");
        }
        return $this->responseJson(404, "Tidak Ada Daftar Category Product");
    }

    public function editCategory(CategoryProductRequest $request, $categoryId) {
        DB::beginTransaction();
        try {
            $validation = $request->validated();
            $doUpdate = CategoryProduct::whereId($categoryId)->update($validation);
            DB::commit();

            return $this->responseJson(200, "Category Product Berhasil Diedit");
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
