<?php

namespace App\Http\Controllers;

use App\Traits\SendNotification;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\FaceVerification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
class registerController extends Controller
{
    use SendNotification;
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fullName' => 'required|max:255',
            'dob' => 'required|date|date_format:Y-m-d',
            'picture' => 'required',
            'gender' => 'required',
            'profession' => 'required|max:255',
            'wilaya_id' => 'required',
            'phone' => 'required|digits:10',
            'email' => 'required|email:rfc,dns,filter',
            'password' => 'required|min:8|max:255',
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

            $checkEmail = User::where('email',$request->email)->first();

            if(!$checkEmail)
            {
                $checkPhone = User::where('phone',$request->phone)->first();
                if(!$checkPhone)
                {

                    $path = '';
                    $folderPath = env('MAIN_PATH') . "profiles/";
                    $image_base64 = base64_decode($request->picture);
                    $path = uniqid() . '.jpg';
                    $file = $folderPath . $path;
                    file_put_contents($file, $image_base64);

                        $user = User::create([
                        'fullName' => $request->fullName,
                        'subName' => (strlen($request->subName) != 0) ? $request->subName : '',
                        'dob' => $request->dob,
                        'picture' => $path,
                        'gender' => $request->gender,
                        'profession' => $request->profession,
                        'wilaya_id' => $request->wilaya_id,
                        'phone' => $request->phone,
                        'email' => $request->email,
                        'password' =>Hash::make($request->password),
                        'is_freelancer' => $request->is_freelancer,
                        'is_verified' => 0,
                        'receive_ads' => $request->receive_ads,
                        'token' => null,
                        'hide_phone' => $request->hide_phone,
                        'is_kaiztech_team' => 0,
                        'company' => (strlen($request->company) != 0) ? $request->company : ''
                    ]);
                    $usr = User::where('id',$user->id)->select('id','fullName','subName',
                    'dob','picture','gender','profession','wilaya_id','phone','email','is_freelancer',
                    'is_verified','receive_ads','token')->first();
                    $token = $usr->createToken('my-app-token')->plainTextToken;

                    $usr->update([
                        'token' => $token
                    ]);

                    return response()->json(['success' => true,'user' => $usr], 200);
                }
                return response()->json(['success' => false,'message' => 1], 200);
            }
            return response()->json(['success' => false,'message' => 2], 200);
        }
    }

    public function HandleFaceDetection(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image1' => 'required',
            'image2' => 'required'
        ]);

        if($validator->fails())
        {
            return response()->json(['success' => false], 200);
        }

        if($validator->validated())
        {
            $id1 = uniqid() . '.jpg';
        $id2 = uniqid() . '.jpg';

        $folderPath = env('MAIN_PATH') . "uploads/";
        $image_base64 = base64_decode($request->image1);
        $file = $folderPath . $id1;
        file_put_contents($file, $image_base64);

        $image_base64 = base64_decode($request->image2);
        $file = $folderPath . $id2;
        file_put_contents($file, $image_base64);

        // $response = $this->faceDetection($request);
        // if($response->status)
        // {

        // }

        $check = FaceVerification::where('user_id',$request->id)->first();
        if($check)
        {
            unlink(env('MAIN_PATH') . 'uploads/' . $check->face1);
            unlink(env('MAIN_PATH') . 'uploads/' . $check->face2);
            FaceVerification::where('user_id',$request->id)->delete();
        }

        FaceVerification::create([
            'face1' => $id1,
            'face2' => $id2,
            'percentage' =>0,
            'user_id' => $request->id,
        ]);

        User::where('id',$request->id)->update([
            'is_verified' => 0
        ]);
        
        $this->sendNotification();
        return response()->json(['success' => true], 200);
        }
    }

    public function faceDetection($request)
    {
        $id1 = uniqid() . '.jpg';
        $id2 = uniqid() . '.jpg';


        $folderPath = env('MAIN_PATH') . "uploads/";
        $image_base64 = base64_decode($request->image1);
        $file = $folderPath . $id1;
        file_put_contents($file, $image_base64);

        $image_base64 = base64_decode($request->image2);
        $file = $folderPath . $id2;
        file_put_contents($file, $image_base64);

        FaceDetect::extract(env('MAIN_PATH') . 'uploads/' . $id1)->save('storage/faces/out' . $id1);
        FaceDetect::extract(env('MAIN_PATH') . 'uploads/' . $id2)->save('storage/faces/out' . $id2);

        // $face = FaceDetection::extract('storage/app/uploads/' . $id1);

        // if($face->found) {
        //     $face->save('storage/faces/out' . $id1);
        //   } else {
        //     return ['status' => 0];
        //   }

        //   $face = FaceDetection::extract('storage/app/uploads/' . $id2);

        // if($face->found) {
        //     $face->save('storage/faces/out' . $id2);
        //   } else {
        //     return ['status' => 0];
        //   }

          $api_key = env('FACEPP_API_KEY');
          $api_secret = env('FACEPP_API_SECRET');
          $facepp = new Facepp($api_key, $api_secret);
           $params = array(
                  'image_url1'   =>  env('STORAGE_FACES_URL') . $id1,
                   'image_url2'   => env('STORAGE_FACES_URL') . $id2
                   );
          $response = $facepp->execute('/compare',$params);
          $response = json_decode(json_encode($response));

     if($response->http_code == 200){
    $data = json_decode($response->body);
    if(intval($data->confidence) <  intval(env('PERCENTAGE')))
    {
        return ['status' => false];
    }
    return ['status' => true , 'face1' => $id1,'face2' => $id2,'percentage' => intval($data->confidence)];
}
else{
    return response()->json(['message' => 'not work '], 200);
}
    }
}
