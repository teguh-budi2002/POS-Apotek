<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApotekRequest;
use App\Models\Apotek;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApotekApiController extends Controller
{
    private $query;

    public function __construct()
    {
        $this->query = Apotek::select("id", "name_of_apotek", "email", "contact_phone", "city", "province", "zip_code", "address", "bio")->newQuery();
    }

    public function addApotek(ApotekRequest $request) {
        DB::beginTransaction();
        try {
            $validation = $request->validated();
            $createApotek = Apotek::create($validation);
            $addedApotek = $this->query->findOrFail($createApotek->id);
            DB::commit();

            return $this->responseJson($addedApotek, 201, "Apotek Baru berhasil dibuat");
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->responseJson(500, $th->getMessage());
        }
    }

     public function getAllApotek() {
        $apoteks = $this->query->get();
        if ($apoteks->isNotEmpty()) {
            return $this->responseJson($apoteks, 200, "Berhasil Mengambil Daftar Apotek");
        }

        return $this->responseJson(404, "Tidak Ada Daftar Apotek");
    }

    public function getPaginateApoteks(Request $request) {
        $perPage = $request->get('rows', 10);
        $page = $request->get('page', 1);

        if ($request->has('search')) {
            $this->query->where('name_of_apotek', 'like', '%' . $request->search . '%');
        }

        $apoteks = $this->query->paginate($perPage, ['*'], 'page', $page);

        if ($apoteks->isNotEmpty()) {
            return $this->responseJson([
                'apoteks' => $apoteks, 
                'total' => $apoteks->total()
            ], 200, "Berhasil Mengambil Daftar Paginasi Apotek");
        }

        return $this->responseJson(404, "Tidak Ada Daftar Paginasi Apotek");
    }

    public function getTrashedApoteks() {
        $trashedApotek = $this->query->onlyTrashed()->get();

        if ($trashedApotek->isNotEmpty()) {
            return $this->responseJson($trashedApotek, 200, "Berhasil Mengambil Daftar Apotek (Trashed)");
        }

        return $this->responseJson(404, "Tidak Ada Daftar Apotek (Trashed)");
    }

    public function restoreTrashedApotek($apotekId) {
        $apotek = Apotek::onlyTrashed()->findOrFail($apotekId);
        $apotek->restore();

        $restoredApotek = $this->query->findOrFail($apotekId);

        return $this->responseJson([
            'restoredApotek' => $restoredApotek
        ], 200, "Berhasil Restore Data Apotek");
    }

    public function editApotek(ApotekRequest $request, $apotekId) {
        DB::beginTransaction();
        try {
            $validation = $request->validated();
            Apotek::whereId($apotekId)->update($validation);
            DB::commit();

            $newUpdatedData = $this->query->findOrFail($apotekId);

            return $this->responseJson([
                'newUpdatedApotek' => $newUpdatedData
            ],200, "Apotek Berhasil Diperbarui");
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->responseJson(500, $th->getMessage());
        }
    }

    public function deleteApotek($apotekId) {
        DB::beginTransaction();
        try {
            Apotek::whereId($apotekId)->delete();
            DB::commit();
            $trashedApotek = $this->query->onlyTrashed()->findOrFail($apotekId);

            return $this->responseJson([
                'trashedApotek' => $trashedApotek
            ], 200, "Apotek Berhasil Dihapus");
        } catch (\Throwable $th) {
            return $this->responseJson(500, $th->getMessage());
        }
    }
}
