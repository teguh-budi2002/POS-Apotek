<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RegisterApiController extends Controller
{
    public function registerProcess(RegisterRequest $request){
        DB::beginTransaction();
        try {
            $validation = $request->validated();
            User::create($validation);
            DB::commit();

            return $this->responseJson(201, 'Regristasi Berhasil');
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->responseJson(500, $th->getMessage());
        }
    }
}
