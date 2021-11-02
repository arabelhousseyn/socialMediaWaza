<?php

namespace App\Http\Controllers\V1\Api;

use App\Models\Freelance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FreelanceController extends Controller
{
    public function index()
    {
        $freelances = Freelance::whereStatus(true)->latest()->paginate(10);
        return response()->json($freelances);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'searched_profile' => 'required',
            'description' => 'required',
            'date' => 'required|date',
            'duration' => 'required',
            'region' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false], 200);
        }
        Freelance::create([
            'user_id' => auth('sanctum')->user()->id,
            'searched_profile' => $request->searched_profile,
            'description' => $request->description,
            'date' => $request->date,
            'duration' => $request->duration,
            'region' => $request->region,
        ]);
        return true;
    }

    public function changeStatus(Request $request)
    {
        $data = Freelance::find($request->id);
        $data->status = !$data->status;
        $data->save();
        return true;
    }
}
