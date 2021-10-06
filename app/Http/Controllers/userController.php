<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\FaceVerification;
use Iman\Streamer\VideoStreamer;
use Auth;
use App\Models\Wilaya;
use App\Models\GroupPost;
use App\Models\Group;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Mail\verificationMail;
use Mail;
use App\Traits\upload;
class userController extends Controller
{
    use upload;
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

    public function getInformationUser($id,$group_post_id = null)
    {
        $following = 0;
        $user = User::where('id',$id)->select('id','fullName','profession','picture','email','phone','hide_phone','wilaya_id','subName','is_kaiztech_team','website')->first();
        if($user)
        {
        $user['fullName'] = $user->subName; 
        $checkFollowing = User::where('id',$id)->with('followers')->first();
        foreach ($checkFollowing->followers as $follow) {
            if($follow->id == Auth::user()->id)
            {
                $following = 1;
                break;
            }
        }
        $post = GroupPost::find($group_post_id);
        if($post)
        {
            $group = Group::find($post->group_id);
            if($group)
            {
                if($group->user_id == $post->user_id)
            {
                if(strlen($user->subName) != 0)
                {
                 $user['picture'] = env('DISPLAY_PATH') .'groupImages/'.$group->cover;
                }else{
                    $user['picture'] = env('DISPLAY_PATH') .'profiles/'.$user->picture;
                }
            }else{
                $user['picture'] = env('DISPLAY_PATH') .'profiles/'.$user->picture;
            }
            }else{
                $user['picture'] = env('DISPLAY_PATH') .'profiles/'.$user->picture;
            }
        }else{
            $user['picture'] = env('DISPLAY_PATH') .'profiles/'.$user->picture;
        }
        $willaya = Wilaya::findOrFail($user->wilaya_id);
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


    public function getAllUsersIds()
   {
    $users = User::pluck('id')->all();
    return response()->json($users, 200);
   }

   public function getUserIdByAuth()
   {
       return response()->json(['user_id' => Auth::user()->id], 200);
   }

   public function searchForUser($name = null)
   {
       if($name == null)
       {
           return response()->json([], 200);
       }else{
        if(strlen($name) >= 3)
        {
            $data = User::where('fullName', 'LIKE', "%{$name}%")
        ->select('id','fullName','profession','picture')->get();
        foreach ($data as $value) {
            $value->picture = env('DISPLAY_PATH') . 'profiles/' . $value->picture;
        }
        return response()->json($data, 200);
        }
        return response()->json([], 200);
       }
   }

   public function getCountOfUsersAccepted()
   {
       $count_users = User::count();
       return response()->json(['count_users' => $count_users], 200);
   }

   public function update(Request $request)
   {
    $path = '';
    $validator = Validator::make($request->all(), [
        'subName' => 'required|max:255',
        'profession' => 'required|max:255',
        'phone' => 'required|digits:10', 
        'email' => 'required|email:rfc,dns,filter',
        'is_freelancer' => 'required',
        'receive_ads' => 'required',
        'hide_phone' => 'required'
    ]);
    if($validator->fails())
    {
        return response()->json(['success' => false], 200);
    }
    if($validator->validated())
    {
        $user = User::find(Auth::user()->id);

        $checkEmail = User::where([['id','<>',Auth::user()->id],['email','=',$request->email]])->first();
        $checkPhone = User::where([['id','<>',Auth::user()->id],['phone','=',$request->phone]])->first();

        if($checkPhone)
        {
            return response()->json(['success' => false,'message' => 1], 200);
        }

        if($checkEmail)
        {
            return response()->json(['success' => false,'message' => 2], 200);
        }

        if(strlen($request->picture) != 0)
        {
            $path = $this->ImageUpload($request->picture,'profiles');
        }
        $updated = User::where('id',Auth::user()->id)->update([
            "picture" => (strlen($path) != 0) ? $path : $user->picture,
            'subName' => $request->subName,
            'profession' => $request->profession,
            'phone' => $request->phone,
            'email' => $request->email,
            'website' => strlen($request->website != 0) ? $request->website : $user->website,
            'receive_ads' => $request->receive_ads,
            'hide_phone' => $request->hide_phone,
            'is_freelancer' => $request->is_freelancer,
        ]);
        if($updated)
        {
            return response()->json(['success' => true], 200);
        }
        return response()->json(['success' => "noway"], 200);
    }
   }

   public function searchGlobal($name = null)
   {
    if($name == null)
    {
        return response()->json([], 200);
    }else{
     if(strlen($name) >= 3)
     {
         $data = User::where('fullName', 'LIKE', "%{$name}%")
     ->select('id','fullName','profession','picture')->get();
     foreach ($data as $value) {
         $value->picture = env('DISPLAY_PATH') . 'profiles/' . $value->picture;
         $value['type_record'] = 0;
     }

     $data2 = Group::where('name', 'LIKE', "%{$name}%")->get();
     foreach ($data2 as $value) {
         $value->cover = env('DISPLAY_PATH') . 'groupImages/' . $value->cover;
         $value->large_cover = env('DISPLAY_PATH') . 'groupImages/' . $value->large_cover;
         $value['type_record'] = 1;
     }
       $updatedItems = $data->merge($data2);
       return response()->json($updatedItems, 200);
     }
     return response()->json([], 200);
    }
   }

   public function test()
   {
    // $firebaseToken = User::pluck('device_token')->all();
    // $SERVER_API_KEY = 'AAAAtX5a_xg:APA91bFCW6XtWkj4OWmkEFLGruyjkcjSNaOpIpFkrWlbvyksPog2LaG08j8ZLiBbi8M3boxZouks9EKvYjDGtJzt27G4ZfkAco9jj_2LPiPwOd96KD_YuhYm0CohvgnT4IBsx4fy__Tk';
    // $data = [
    //     "registration_ids" => $firebaseToken,
    //     "notification" => [
    //         "title" => 'Nouveaux invitation',
    //         "body" => "test",
    //         'image' => 'https://dashboard.waza.fun/waza-small.png',
    //         'sound' => true,
    //     ]
    // ];
    // $dataString = json_encode($data);
    // $headers = [
    //     'Authorization: key=' . $SERVER_API_KEY,
    //     'Content-Type: application/json',
    // ];
    // $ch = curl_init();
    // curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
    // curl_setopt($ch, CURLOPT_POST, true);
    // curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
    // $result = curl_exec($ch );
    //     curl_close( $ch );
    //     return $result;

    $details = [
        "title" => "test",
        "body" => "test"
    ];

    $hi = Mail::to('hocine.arab1@hotmail.com')->send(new verificationMail($details));
    return response()->json($hi, 200);
   }
}
