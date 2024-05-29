<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApotekRequest;
use App\Models\Apotek;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApotekApiController extends Controller
{
    public function addApotek(ApotekRequest $request) {
        DB::beginTransaction();
        try {
            $validation = $request->validated();
            Apotek::create($validation);
            DB::commit();

            return $this->responseJson(201, "Apotek Baru berhasil dibuat");
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->responseJson(500, $th->getMessage());
        }
    }

     public function getAllApotek() {
        $apoteks = Apotek::get();
        if ($apoteks->isNotEmpty()) {
            return $this->responseJson($apoteks, 200, "Berhasil Mengambil Daftar Apotek");
        }

        return $this->responseJson(404, "Tidak Ada Daftar Apotek");
    }

    public function editApotek(ApotekRequest $request, $apotekId) {
        DB::beginTransaction();
        try {
            $validation = $request->validated();
            Apotek::whereId($apotekId)->update($validation);
            DB::commit();

            return $this->responseJson(201, "Apotek Berhasil Diperbarui");
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
    
            return $this->responseJson(200, "Apotek Berhasil Dihapus");
        } catch (\Throwable $th) {
            return $this->responseJson(500, $th->getMessage());
        }
    }
}
