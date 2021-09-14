<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class versionappController extends Controller
{
    public function index($version)
    {
        $check = DB::table('useful_data')->where('data',$version)->first();
        if($check)
        {
            $response = [
                'success' => true
            ];
            return response()->json($response, 200);
        }else{
            $response = [
                'success' => false
            ];
            return response()->json($response, 200); 
        }
    }
}
