<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserApiController extends Controller
{
    public function getNameOfUser() {
        $users = User::get("name");

        return $users->isNotEmpty()
            ? $this->responseJson($users, 200, "Berhasil mengambil daftar nama user")
            : $this->responseJson(null, 404, "User tidak ditemukan");
    }
}
