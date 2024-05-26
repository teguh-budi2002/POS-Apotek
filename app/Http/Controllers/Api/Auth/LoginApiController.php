<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginApiController extends Controller
{
    public function loginProcess(LoginRequest $request) {
        try {
            $validation = $request->validated();
            if (!Auth::attempt($validation)) {
                return $this->responseJson(401, "Pengguna Tidak Ditemukan");
            }

            $user = User::where('email', $request->email)->first();
            $token = $user->createToken("auth_token", ["*"], now()->addDay());

            return $this->responseJson(['token' => $token->plainTextToken], 200, 'Login Berhasil');
        } catch (\Throwable $th) {
            return $this->responseJson(500, $th->getMessage());
        }
    }
}
