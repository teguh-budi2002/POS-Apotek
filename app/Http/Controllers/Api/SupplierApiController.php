<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SupplierRequest;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierApiController extends Controller
{
    public function addSupplier(SupplierRequest $request) {
        DB::beginTransaction();
        try {
            $validation = $request->validated();
            Supplier::create($validation);
            DB::commit();

            return $this->responseJson(201, "Supplier Baru berhasil dibuat");
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->responseJson(500, $th->getMessage());
        }
    }

     public function getAllSupplier() {
        $suppliers = Supplier::get();
        if ($suppliers->isNotEmpty()) {
            return $this->responseJson($suppliers, 200, "Berhasil Mengambil Daftar Supplier");
        }

        return $this->responseJson(404, "Tidak Ada Daftar Supplier");
    }

    public function editSupplier(SupplierRequest $request, $supplierId) {
        DB::beginTransaction();
        try {
            $validation = $request->validated();
            Supplier::whereId($supplierId)->update($validation);
            DB::commit();

            return $this->responseJson(201, "Supplier Berhasil Diperbarui");
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->responseJson(500, $th->getMessage());
        }
    }

    public function deleteSupplier($supplierId) {
        DB::beginTransaction();
        try {
            Supplier::whereId($supplierId)->delete();
            DB::commit();
    
            return $this->responseJson(200, "Supplier Berhasil Dihapus");
        } catch (\Throwable $th) {
            return $this->responseJson(500, $th->getMessage());
        }
    }
}
