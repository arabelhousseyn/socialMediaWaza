<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\FaceVerification;
use Iman\Streamer\VideoStreamer;
use Auth;
use App\Models\Wilaya;
class userController extends Controller
{
    public function approve($id)
    {
        $storage_face = env('STORAGE_FACES_URL');
        $storage_face = str_replace('out','',$storage_face);
        $user = User::with('verification')->findOrFail($id);
        $instanceVerification = FaceVerification::findOrFail($user->verification->id);
        if(@unlink('storage/faces/' .$instanceVerification->face1) && @unlink('storage/faces/' .$instanceVerification->face2)
         && @unlink('storage/app/uploads/' .str_replace('out','',$instanceVerification->face1))
         && @unlink('storage/app/uploads/' .str_replace('out','',$instanceVerification->face2)))
        {
            FaceVerification::where('id',$instanceVerification->id)->delete();

            return response()->json(['success' => true], 200);
        }
        return response()->json(['success' => false], 200);// to be changed

    }

    public function checkIfApproved($id)
    {
        $user = User::find($id);
        if($user)
        {
            return response()->json(['success' => $user->is_verified,'UserId' => $id], 200);
        }
        return response()->json(['success' => 0], 200);
    }

    public function getVerificationByUser($id)
    {
        $user = User::with('verification')->findOrFail($id);
        return response()->json(['success' => true,'data' => $user], 200);
    }

    public function getUsersNotVeirifed()
    {
        $users = User::with('verification')->where('is_verified',0)->get();
        return response()->json($users, 200);
    }

    public function getInformationUser($id)
    {
        $following = 0;
        $user = User::where('id',$id)->select('id','fullName','profession','picture','email','phone','hide_phone','wilaya_id')->first();
        if($user)
        {
        $checkFollowing = User::where('id',$id)->with('followers')->first();
        foreach ($checkFollowing->followers as $follow) {
            if($follow->id == Auth::user()->id)
            {
                $following = 1;
                break;
            }
        }
        
        $willaya = Wilaya::where('id',$user->wilaya_id)->first();
        $user['wilaya_name'] = $willaya->name;
        if($user['hide_phone'] == 1)
        {
            $user['phone'] = '';
        }

        $user['following'] = $following;

        return response()->json($user, 200);
        }
        return response()->json(['success' => false], 200);
    }
}
