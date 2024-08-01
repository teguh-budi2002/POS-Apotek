<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryProductRequest;
use App\Models\CategoryProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryProductApiController extends Controller
{
    private $query;

    public function __construct() {
         $this->query = CategoryProduct::select(
            'id',
            'name',
            'isActive'
         )
         ->newQuery();
    }

    public function createCategory(CategoryProductRequest $request) {
        DB::beginTransaction();
        try {
            $validation = $request->validated();
            $createCategory = CategoryProduct::create($validation);
            $addedCategory = $this->query->findOrFail($createCategory->id);
            DB::commit();
            
            return $this->responseJson($addedCategory, 201, "Category Product Berhasil Dibuat");
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->responseJson(500, $th->getMessage());
        }
    }

    public function getCategories() {
        $categories = $this->query->isActive()->get();

        return $categories->isNotEmpty()
            ? $this->responseJson(['categories' => $categories], 200, "Berhasil Mengambil Daftar Kategori Produk")
            : $this->responseJson(404, "Tidak Ada Daftar Kategori Produk");
    }

    public function getPaginateCategories(Request $request) {
        $perPage = $request->get('rows', 10);
        $page = $request->get('page', 1);

        $this->query->filterCategory($request->search);

        $categories = $this->query->paginate($perPage, ['*'], 'page', $page);

        return $categories->isNotEmpty()
            ? $this->responseJson(['categories' => $categories, 'total' => $categories->total()], 200, "Berhasil Mengambil Daftar Paginasi Kategori Produk")
            : $this->responseJson(404, "Tidak Ada Daftar Paginasi Kategori Produk");
    }

    public function getTrashedCategories() {
        $trashedCategory = $this->query->onlyTrashed()->get();

        return $trashedCategory->isNotEmpty()
            ? $this->responseJson($trashedCategory, 200, "Berhasil Mengambil Daftar Kategori (Trashed)")
            : $this->responseJson(404, "Tidak Ada Daftar Kategori (Trashed)");
    }

    public function restoreTrashedCategory($categoryId) {
        $category = CategoryProduct::onlyTrashed()->findOrFail($categoryId);
        $category->restore();

        $restoredCategory = $this->query->findOrFail($categoryId);

        return $this->responseJson([
            'restoredCategory' => $restoredCategory
        ], 200, "Berhasil Restore Data Category");
    }

    public function editCategory(CategoryProductRequest $request, $categoryId) {
        DB::beginTransaction();
        try {
            $validation = $request->validated();
            CategoryProduct::whereId($categoryId)->update($validation);
            DB::commit();

            $newUpdatedData = $this->query->findOrFail($categoryId);

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

            $newUpdatedData = $this->query->findOrFail($categoryId);

            return $this->responseJson([
                'newUpdatedCategory' => $newUpdatedData
            ],200, "Status Category Product Berhasil Diedit");
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->responseJson(500, $th->getMessage());
        }
    }

    public function deleteCategory($categoryId) {
        DB::beginTransaction();
        try {
            CategoryProduct::whereId($categoryId)->delete();
            DB::commit();
            $trashedCategory = $this->query->onlyTrashed()->findOrFail($categoryId);

            return $this->responseJson([
                'trashedCategory' => $trashedCategory
            ], 200, "Category Product Berhasil Dihapus");
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->responseJson(500, $th->getMessage());
        }
    }
}
