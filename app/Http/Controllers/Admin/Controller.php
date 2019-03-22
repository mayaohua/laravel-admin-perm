<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ApiResponse;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Input;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, ApiResponse;


    protected function getLimits(){
        $limits = [ 50 ,100, 200, 300, 400];

        $limit = Input::get('limit',$limits[0]);

        $limit = in_array($limit,$limits) ? $limit : $limits[0];
        return [$limits,$limit];
    }

}
