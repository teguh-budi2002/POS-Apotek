<?php

namespace App\Http\Controllers;

use InvalidArgumentException;

abstract class Controller
{
    /**
     * Custom Response
     * 
     * Default Data = []
     * Default Status = 200
     * Default Message = 'Default Message'
     * 
     * return JsonResponse object
     */

    public function responseJson() {
        $args = func_get_args();
        $data = [];
        $status = 200;
        $message = 'Default Message';

        if (count($args) == 2) {
            list($status, $message) = $args;
        } elseif (count($args) == 3) {
            list($data, $status, $message) = $args;
        } else {
            throw new InvalidArgumentException('Maximum number of arguments has been reached');
        }

        $wrap = [
            'status_code' => $status,
            'message' => $message,
            'datas' => $data
        ];

        return response()->json($wrap, $status)->withHeaders([
            'Content-Type' => 'application/json'
        ]);
    }
}
