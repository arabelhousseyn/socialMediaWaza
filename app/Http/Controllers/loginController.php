<?php

namespace App\Http\Controllers;

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
            'phone' => 'required',
            'password' => 'required',
            'country_code' => 'required'
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
                 'token' => $token
             ]);
             $willaya = Wilaya::with('country')->where('id',$user->wilaya_id)->first();
             $user->wilaya_id = $willaya;
        
            $response = [
                'success' => true,
                'user' => $user,
            ];
        
             return response($response, 200);
        }
    }
}
