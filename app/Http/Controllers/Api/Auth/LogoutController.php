<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class LogoutController extends Controller
{
    public function logout(Request $request) {
        $currentAccessToken = $request->bearerToken();
        
        $accessToken = PersonalAccessToken::findToken($currentAccessToken);
    
        if (!$accessToken) {
            return $this->responseJson(404, "Akses Token Tidak Ada");
        }

        $authUser = $accessToken->tokenable;
        
        if ($authUser) {
            $accessToken->delete();

            return $this->responseJson(200, "Logout Berhasil");
        }

        return $this->responseJson(400, "Pengguna dan Token Tidak Ditemukan.");

    }
}
