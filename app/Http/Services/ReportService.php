<?php

namespace App\Http\Services;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Auth;
class ReportService{

    public function store($request)
    {
        $validator = Validator::make($request->all(), [
            'reportable_id' => 'required',
            'reportable_type' => 'required',
        ]);

        if($validator->fails())
        {
            return response()->json(['success' => false], 200);
        }

        if($validator->validated())
        {
            $report = Report::create([
                'user_id' => Auth::user()->id,
                'reportable_id' => $request->reportable_id,
                'reportable_type' => $request->reportable_type
            ]);

            return response()->json(['success' => true], 200);
        }
    }
}