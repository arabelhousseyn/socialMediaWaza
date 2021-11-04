<?php

namespace App\Http\Controllers\V1\Api;

use App\Models\CvLibrary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CvLibraryController extends Controller
{
    public function index()
    {
        $cv = CvLibrary::find(Auth::id());
        return response()->json($cv);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'professional_details' => 'required',
            'formations' => 'required',
            'professional_experience' => 'required',
            'language' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false], 200);
        }
        CvLibrary::create([
            'user_id' => Auth::id(),
            'professional_details' => $request->professional_details,
            'formations' => $request->formations,
            'professional_experience' => $request->professional_experience,
            'language' => $request->language,
        ]);
        return true;
    }

    public function increment(Request $request)
    {
        CvLibrary::find($request->cv_library_id)->increment('seen');
        return true;
    }

    public function getAllCv()
    {
        $allCv = CvLibrary::latest()->paginate(7);
        return response()->json($allCv);
    }

}
