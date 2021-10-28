<?php

namespace App\Http\Controllers\V1\Api;

use Illuminate\Http\Request;
use App\Models\Wilaya;
class willayaController extends Controller
{
    public function index($id)
    {
        $data = Wilaya::Bycountry($id)->get();
        return response()->json($data, 200);
    }

    public function index2($id)
    {
        $data = Wilaya::on('mysql2')->Bycountry($id)->get();
        return response()->json($data, 200);
    }
}
