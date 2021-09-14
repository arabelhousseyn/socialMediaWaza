<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Wilaya;
class willayaController extends Controller
{
    public function index($id)
    {
          return Wilaya::Bycountry($id)->get();
    }
}
