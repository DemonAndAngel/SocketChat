<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Response;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    //json数据返回格式化
    public function makeApiResponse($data, $msg='', $code = 200 ){
        $response = Response::make([
            'meta' => [
                'code' => $code,
                'msg' => $msg
            ],
            'data' => $data ,
        ]);
        $response->header('Content-Type', 'application/json');
        return $response;
    }
}
