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
            $createUnit =  UnitProduct::create($validation);
            $addedUnit = UnitProduct::select("id", "name", "isActive")->findOrFail($createUnit->id);
            DB::commit();
            
            return $this->responseJson($addedUnit, 201, "Satuan Produk Berhasil Dibuat");
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->responseJson(500, $th->getMessage());
        }
    }

    public function getUnits() {
        $unit = UnitProduct::select("id", "name", "isActive")->get();
        if ($unit->isNotEmpty()) {
            return $this->responseJson($unit, 200, "Berhasil Mengambil Daftar Satuan Produk");
        }
        return $this->responseJson(404, "Tidak Ada Daftar Satuan Produk");
    }

    public function getPaginateUnits(Request $request) {
        $perPage = $request->get('rows', 10);
        $page = $request->get('page', 1);
        $query = UnitProduct::query();

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $units = $query->paginate($perPage, ['*'], 'page', $page);

        if ($units->isNotEmpty()) {
            return $this->responseJson([
                'units' => $units, 
                'total' => $units->total()
            ], 200, "Berhasil Mengambil Daftar Paginasi Satuan Produk");
        }

        return $this->responseJson(404, "Tidak Ada Daftar Paginasi Satuan Produk");
    }

    public function getTrashedUnits() {
        $trashedUnit = UnitProduct::onlyTrashed()->select("id", "name", "isActive")->get();

        if ($trashedUnit->isNotEmpty()) {
            return $this->responseJson($trashedUnit, 200, "Berhasil Mengambil Daftar Unit (Trashed)");
        }

        return $this->responseJson(404, "Tidak Ada Daftar Unit (Trashed)");
    }

    public function restoreTrashedUnit($unitId) {
        $unit = UnitProduct::onlyTrashed()->findOrFail($unitId);
        $unit->restore();

        $restoredUnit = UnitProduct::select("id", "name", "isActive")->findOrFail($unitId);

        return $this->responseJson([
            'restoredUnit' => $restoredUnit
        ], 200, "Berhasil Restore Data Unit");
    }

    public function editUnit(UnitProductRequest $request, $unitId) {
        DB::beginTransaction();
        try {
            $validation = $request->validated();
            $doUpdate = UnitProduct::whereId($unitId)->update($validation);
            DB::commit();

            $newUpdatedData = UnitProduct::whereId($unitId)->firstOrFail();

            return $this->responseJson([
                'newUpdatedUnit' => $newUpdatedData
            ],200, "Satuan Produk Berhasil Diedit");
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->responseJson(500, $th->getMessage());
        }
    }

    public function setStatusUnit(Request $request, $unitId) {
        DB::beginTransaction();
        try {
            UnitProduct::whereId($unitId)->update([
                'isActive' => $request->status
            ]);
            DB::commit();

            $newUpdatedData = UnitProduct::whereId($unitId)->firstOrFail();

            return $this->responseJson([
                'newUpdatedUnit' => $newUpdatedData
            ],200, "Status Unit Product Berhasil Diedit");
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->responseJson(500, $th->getMessage());
        }
    }

    public function deleteUnit($unitId) { 
        $doDelete = UnitProduct::whereId($unitId)->delete();
        $trashedUnit = UnitProduct::onlyTrashed()->select("id", "name", "isActive")->findOrFail($unitId);
        return $this->responseJson([
            'trashedUnit' => $trashedUnit
        ], 200, "Satuan Produk Berhasil Dihapus");
    }
}
