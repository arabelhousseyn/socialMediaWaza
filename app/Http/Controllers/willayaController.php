<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Wilaya;
class willayaController extends Controller
{
    public function index($id)
    {
        $data = Wilaya::Bycountry($id)->get();
        return response()->json($data, 200);
    }
}
