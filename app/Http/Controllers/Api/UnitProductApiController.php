<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UnitProductRequest;
use App\Models\UnitProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UnitProductApiController extends Controller
{
     public function createUnit(UnitProductRequest $request) {
        DB::beginTransaction();
        try {
            $validation = $request->validated();
            UnitProduct::create($validation);
            DB::commit();
            
            return $this->responseJson(201, "Satuan Produk Berhasil Dibuat");
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->responseJson(500, $th->getMessage());
        }
    }

    public function getUnits() {
        $unit = UnitProduct::get();
        if ($unit->isNotEmpty()) {
            return $this->responseJson($unit, 200, "Berhasil Mengambil Daftar Satuan Produk");
        }
        return $this->responseJson(404, "Tidak Ada Daftar Satuan Produk");
    }

    public function editUnit(unitProductRequest $request, $unitId) {
        DB::beginTransaction();
        try {
            $validation = $request->validated();
            $doUpdate = UnitProduct::whereId($unitId)->update($validation);
            DB::commit();

            return $this->responseJson(200, "Satuan Produk Berhasil Diedit");
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->responseJson(500, $th->getMessage());
        }
    }

    public function deleteUnit($unitId) { 
        $doDelete = UnitProduct::whereId($unitId)->delete();
        return $this->responseJson(200, "Satuan Produk Berhasil Dihapus");
    }
}
