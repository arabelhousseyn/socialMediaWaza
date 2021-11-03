<?php

namespace App\Http\Controllers\V1\Api;

use App\Http\Controllers\Controller;
use App\Mail\ChangePasswordNotificationMail;
use App\Models\GroupPost;
use App\Models\User;
use App\Rules\MatchOldPassword;
use App\Traits\upload;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    use upload;

    public function getProfileData()
    {
        $user = User::select('id','fullName','profession','picture')->withCount('followers')->with('followers.user:id,picture')->find(Auth::id());
        return response()->json($user, 200);
    }

    public function updateProfileData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fullName' => 'required',
            'subName' => 'required',
            'dob' => 'required',
            'gender' => 'required',
            'profession' => 'required',
            'wilaya_id' => 'required',
            'phone' => 'required',
            'is_freelancer' => 'required|boolean',
            'hide_phone' => 'required|boolean',
            'company' => 'nullable',
            'website' => 'nullable|url',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false], 200);
        }
        if($validator->validated())
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
        }
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

    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => ['required', new MatchOldPassword(), 'min:8', 'max:50'],
            'new_password' => ['required', 'min:8', 'max:50'],
            'new_confirm_password' => ['same:new_password'],
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false], 200);
        }
        $message = 'Votre mot de passe a été modifié le : ' . Carbon::now()->format('d-m-Y , H:i');
        $user = User::find(Auth::id());
        $user->update(['password' => Hash::make($request->new_password)]);
        $details = [
            'subject' => 'Changement de mot de passe',
            'text' => 'Bonjour  <strong>' . $user->fullName . '</strong><br>' . $message . '',
        ];
        Mail::to($user->email)->send(new ChangePasswordNotificationMail($details));
        return true;
    }

    public function getAllPublications(Request $request)
    {
        $group_posts = GroupPost::where('id', Auth::id())->paginate(20);
        return response()->json($group_posts, 200);
    }
}
