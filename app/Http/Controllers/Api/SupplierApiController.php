<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SupplierRequest;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierApiController extends Controller
{
    private $query;

    public function __construct()
    {
        $this->query = Supplier::select("id", "supplier_name", "email", "contact_phone", "city", "province", "zip_code", "address", "description")->newQuery();
    }

    public function addSupplier(SupplierRequest $request) {
        DB::beginTransaction();
        try {
            $validation = $request->validated();
            $createSupplier = Supplier::create($validation);
            $addedSupplier = $this->query->findOrFail($createSupplier->id);
            DB::commit();

            return $this->responseJson($addedSupplier, 201, "Supplier Baru berhasil dibuat");
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

     public function getPaginateSuppliers(Request $request) {
        $perPage = $request->get('rows', 10);
        $page = $request->get('page', 1);

        if ($request->has('search')) {
            $this->query->where('supplier_name', 'like', '%' . $request->search . '%');
        }

        $suppliers = $this->query->paginate($perPage, ['*'], 'page', $page);

        return $suppliers->isNotEmpty()
                ? $this->responseJson(['suppliers' => $suppliers, 'total' => $suppliers->total()], 200, "Berhasil Mengambil Daftar Supplier")
                : $this->responseJson(404, "Tidak Ada Daftar Supplier");
    }

    public function getTrashedSuppliers() {
        $trashedSupplier = $this->query->onlyTrashed()->get();

        return $trashedSupplier->isNotEmpty() 
            ? $this->responseJson($trashedSupplier, 200, "Berhasil Mengambil Daftar Supplier (Trashed)")
            : $this->responseJson(404, "Tidak Ada Daftar Supplier (Trashed)");
    }

    public function restoreTrashedSupplier($supplierId) {
        $supplier = Supplier::onlyTrashed()->findOrFail($supplierId);
        $supplier->restore();

        $restoredSupplier = $this->query->findOrFail($supplierId);

        return $this->responseJson([
            'restoredSupplier' => $restoredSupplier
        ], 200, "Berhasil Restore Data Supplier");
    }

    public function editSupplier(SupplierRequest $request, $supplierId) {
        DB::beginTransaction();
        try {
            $validation = $request->validated();
            Supplier::whereId($supplierId)->update($validation);
            DB::commit();

            $newUpdatedData = $this->query->findOrFail($supplierId);

            return $this->responseJson([
                'newUpdatedSupplier' => $newUpdatedData
            ], 200, "Supplier Berhasil Diperbarui");
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
            $trashedSupplier = $this->query->onlyTrashed()->findOrFail($supplierId);
    
            return $this->responseJson([
                'trashedSupplier' => $trashedSupplier
            ], 200, "Supplier Berhasil Dihapus");
        } catch (\Throwable $th) {
            return $this->responseJson(500, $th->getMessage());
        }
    }
}
