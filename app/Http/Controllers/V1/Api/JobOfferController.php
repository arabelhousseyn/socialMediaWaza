<?php

namespace App\Http\Controllers\V1\Api;

use App\Models\JobOffer;
use App\Traits\upload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class JobOfferController extends Controller
{
    use upload;

    public function index()
    {
        $Job_offers = JobOffer::whereStatus(true)->latest()->paginate(10);
        return response()->json($Job_offers);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_logo' => 'required',
            'searched_profile' => 'required',
            'company_name' => 'required',
            'description' => 'nullable',
            'number' => 'integer',
            'workplace' => 'required',
            'type_of_contract' => 'required|in:CDD,CDI',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false], 200);
        }
        $path = $this->ImageUpload($request->company_logo, 'companyLogos');
        JobOffer::create([
            'user_id' => auth('sanctum')->user()->id,
            'company_logo' => env('DISPLAY_PATH') . 'companyLogos/' . $path,
            'searched_profile' => $request->date,
            'company_name' => $request->date,
            'description' => $request->date,
            'number' => $request->date,
            'workplace' => $request->date,
            'type_of_contract' => $request->date,
        ]);
        return true;
    }

    public function changeStatus(Request $request)
    {
        $data = JobOffer::find($request->id);
        $data->status = !$data->status;
        $data->save();
        return true;
    }
}
