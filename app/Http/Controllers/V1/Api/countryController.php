<?php

namespace App\Http\Controllers\V1\Api;

use Illuminate\Http\Request;
use App\Models\Country;
class countryController extends Controller
{
    public function index()
    {
        // get all countries
        $data = Country::all();
        return response()->json($data, 200);
    }

    public function index2()
    {
        // get all countries
        $data = Country::on('mysql2')->all();
        return response()->json($data, 200);
    }
}
