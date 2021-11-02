<?php

namespace App\Http\Controllers\V1\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\upload;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    use upload;

    public function getProfileData()
    {
        $user = User::find(auth('sanctum')->user()->id);
        return response()->json([
            'data' => $user,
            'success' => true,
        ], 200);
    }

    public function updateProfileData(Request $request)
    {
        User::where('id', auth('sanctum')->user()->id)->update([
            'fullName' => $request->fullName,
            'subName' => $request->subName,
            'dob' => $request->dob,
            'gender' => $request->gender,
            'profession' => $request->profession,
            'wilaya_id' => $request->wilaya_id,
            'phone' => $request->phone,
            'is_freelancer' => $request->is_freelancer,
            'hide_phone' => $request->hide_phone,
            'company' => $request->company,
            'website' => $request->website,
        ]);
        return true;
    }

    public function updateProfilePicture(Request $request)
    {
        $path = $this->ImageUpload($request->picture, 'profiles');
        User::where('id', auth('sanctum')->user()->id)->update([
            'picture' => $path
        ]);
        return true;
    }

    public function getAllPublications(Request $request)
    {
        $group_posts = User::where('id', auth('sanctum')->user()->id)->paginate(20);
        return response()->json([
            $group_posts,
            'success' => true,
        ], 200);
    }
}
