<?php

namespace App\Http\Controllers\V1\Api;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Wilaya;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
class loginController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|digits:10',
            'password' => 'required',
            'country_code' => 'required',
            'device_token' => 'required'
        ]);

        if($validator->fails())
        {
            return response()->json(['success' => false], 200);
        }

        if($validator->validated())
        {
            $user= User::where('phone', $request->phone)->first();
            
            if (!$user || !Hash::check($request->password, $user->password)) {
                $response = [
                    'success' => false
                ];
                return response()->json($response, 200);
            }

            $wilaya = Wilaya::with('country')->where('id',$user->wilaya_id)->first();
            if($wilaya->country->code != $request->country_code)
            {
                $response = [
                    'success' => false
                ];
                return response()->json($response, 200);   
            }
        
             $token = $user->createToken('my-app-token')->plainTextToken;

             $user->update([
                 'token' => $token,
                 'device_token' => (@$request->device_token) ? $request->device_token : $user->device_token,
             ]);
             
             $user['wilaya_name'] = $wilaya->name;
        
            $response = [
                'success' => true,
                'user' => $user,
            ];
        
             return response($response, 200);
        }
    }
}
