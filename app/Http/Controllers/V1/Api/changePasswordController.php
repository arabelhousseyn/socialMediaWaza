<?php

namespace App\Http\Controllers\V1\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Auth;
class changePasswordController extends Controller
{
    public function index(Request $request)
    {
        // change password for current account
        $validator = Validator::make($request->all(), [
            'is_freelancer' => 'required',
            'receive_ads' => 'required',
            'hide_phone' => 'required',
        ]);
        if($validator->fails())
        {
            return response()->json(['success' => false], 200);
        }
        if($validator->validated())
        {
            $user = User::select('password')->find(Auth::user()->id);
            if($user)
            {
                if(Hash::check($request->old_password, $user->password))
                {
                   $updatePasswword = User::where('id',Auth::user()->id)->update([
                       'password' => Hash::make($request->new_password),
                       'is_freelancer' => $request->is_freelancer,
                       'receive_ads' => $request->receive_ads,
                       'hide_phone' => $request->hide_phone,
                   ]); 
                   if($updatePasswword)
                   {
                    return response()->json(['success' => true], 200);
                   }
                   return response()->json(['success' => false], 200);
                }else{
                    $update = User::where('id',Auth::user()->id)->update([
                        'is_freelancer' => $request->is_freelancer,
                        'receive_ads' => $request->receive_ads,
                        'hide_phone' => $request->hide_phone,
                    ]);
                    if($update)
                    {
                        return response()->json(['success' => true,'password' => 'échec'], 200);
                    }
                    return response()->json(['success' => false], 200);
                }
            }
            return response()->json(['success' => false], 200);
        }
    }
}
