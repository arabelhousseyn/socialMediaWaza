<?php

namespace App\Http\Controllers\V1\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\upload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    use upload;

    public function getProfileData()
    {
        $user = User::find(Auth::id());
        return response()->json($user, 200);
    }

    public function updateProfileData(Request $request)
    {
        User::where('id', Auth::id())->update([
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
        return response()->json(['success' => true], 200);
    }

    public function updateProfilePicture(Request $request)
    {
        $path = $this->ImageUpload($request->picture, 'profiles');
        User::where('id', Auth::id())->update([
            'picture' => $path
        ]);
        return response()->json(['success' => true], 200);
    }

    public function getAllPublications(Request $request)
    {
        $group_posts = User::where('id', Auth::id())->paginate(20);
        return response()->json($group_posts, 200);
    }
}
