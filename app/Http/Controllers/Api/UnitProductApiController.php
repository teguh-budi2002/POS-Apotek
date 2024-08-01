<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UnitProductRequest;
use App\Models\UnitProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UnitProductApiController extends Controller
{
    private $query;

    public function __construct()
    {
        $this->query = UnitProduct::select(
            "id", 
            "name", 
            "isActive"
        )->newQuery();
    }
    public function createUnit(UnitProductRequest $request) {
        DB::beginTransaction();
        try {
            $validation = $request->validated();
            $createUnit =  UnitProduct::create($validation);
            $addedUnit = $this->query->findOrFail($createUnit->id);
            DB::commit();
            
            return $this->responseJson($addedUnit, 201, "Satuan Produk Berhasil Dibuat");
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->responseJson(500, $th->getMessage());
        }
    }

    public function getUnits() {
        $units = $this->query->isActive()->get();
   
        return $units->isNotEmpty()
            ? $this->responseJson($units, 200, "Berhasil Mengambil Daftar Satuan Produk")
            : $this->responseJson(404, "Tidak Ada Daftar Satuan Produk");
    }

    public function getPaginateUnits(Request $request) {
        $perPage = $request->get('rows', 10);
        $page = $request->get('page', 1);
        
        $this->query->filterUnit($request->search);

        $units = $this->query->paginate($perPage, ['*'], 'page', $page);

        return $units->isNotEmpty()
            ? $this->responseJson(['units' => $units, 'total' => $units->total()], 200, "Berhasil Mengambil Daftar Paginasi Satuan Produk")
            : $this->responseJson(404, "Tidak Ada Daftar Paginasi Satuan Produk");
    }

    public function getTrashedUnits() {
        $trashedUnit = $this->query->onlyTrashed()->get();

        return $trashedUnit->isNotEmpty()
            ? $this->responseJson($trashedUnit, 200, "Berhasil Mengambil Daftar Unit (Trashed)")
            : $this->responseJson(404, "Tidak Ada Daftar Unit (Trashed)");
    }

    public function restoreTrashedUnit($unitId) {
        $unit = UnitProduct::onlyTrashed()->findOrFail($unitId);
        $unit->restore();

        $restoredUnit = $this->query->findOrFail($unitId);

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

            $newUpdatedData = $this->query->findOrFail($unitId);

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

            $newUpdatedData = $this->query->findOrFail($unitId);

            return $this->responseJson([
                'newUpdatedUnit' => $newUpdatedData
            ],200, "Status Unit Product Berhasil Diedit");
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->responseJson(500, $th->getMessage());
        }
    }

    public function deleteUnit($unitId) { 
        DB::beginTransaction();
        try {
            UnitProduct::whereId($unitId)->delete();
            DB::commit();
            $trashedUnit = $this->query->onlyTrashed()->findOrFail($unitId);
            
            return $this->responseJson([
                'trashedUnit' => $trashedUnit
            ], 200, "Satuan Produk Berhasil Dihapus");
            
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->responseJson(500, $th->getMessage());
        }
    }
}
