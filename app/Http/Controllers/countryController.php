<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Country;
class countryController extends Controller
{
    public function index()
    {
        $data = Country::all();
        return response()->json($data, 200);
    }
}
